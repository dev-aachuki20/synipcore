<?php

namespace App\Http\Controllers\Api\UserApp;

use App\Http\Controllers\Api\APIController;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityController extends APIController
{
    public function todayActivities()
    {
        try {
            $user = auth()->user();

            $today = Carbon::today();

            // Assuming the user has building_id and unit_id
            $buildingId = $user->building_id;
            $unitId = $user->unit_id;

            // $visitors = $user->visitors()->whereStatus('approved')->whereDate('visit_date', $today)
            //     ->select('id', 'visit_date', 'name', 'visitor_type', 'phone_number', 'visitor_note')
            //     ->get()

            $visitors = Visitor::whereIn('status', ['approved', 'in'])
                ->whereDate('visit_date', $today)
                ->where(function ($query) use ($user, $buildingId, $unitId) {
                    // Visitors created by the user
                    $user->visitors()
                        // OR visitors linked to the same building and unit of the user
                        ->orWhereHas('user', function ($subQuery) use ($buildingId, $unitId) {
                            $subQuery->where('building_id', $buildingId)
                                ->where('unit_id', $unitId);
                        });
                })
                ->select('id', 'visit_date', 'name', 'visitor_type', 'phone_number', 'visitor_note')
                ->get()
                ->map(function ($visitor) {
                    $name = $visitor->name;
                    $visitorType = $visitor->visitor_type;
                    return [
                        'id'            => $visitor->id,
                        'name'          => $visitorType . ' | ' . $name,
                        'type'          => 'visitor',
                        'activity_type' => 'PRE APPROVED',
                        'visitor_type'  => $visitorType,
                        'visitor_phone'  => $visitor->phone_number ?? '',
                        'visitor_note'   => $visitor->visitor_note ?? '',
                        'visitor_other_info'   => $visitor->other_info ?? '',
                        'activity_date'  => $visitor->visit_date->format(config('constant.date_format.date_time')),
                        // 'activity_time' => $visitor->visit_date->format(config('constant.date_format.time')),
                    ];
                });

            $bookedServices = $user->bookedServices()->whereDate('booking_date', $today)
                ->select('id', 'service_id', 'booking_date')
                ->get()
                ->map(function ($bookedService) {
                    return [
                        'id'            => $bookedService->id,
                        'name'          => $bookedService->service->title ?? '',
                        'type'          => 'service_booking',
                        'activity_type' => 'SERVICE BOOKED',
                        'activity_date' => $bookedService->booking_date->format('Y-m-d')
                        // 'activity_time' => $bookedService->booking_date->format(config('constant.date_format.time')),
                    ];
                });

            // $dailyHelps = $user->dailyHelps()->whereDate('created_at', $today)->get();

            // $combinedData = $visitors->merge($bookedServices)->merge($dailyHelps)->sortBy('created_at');
            $activities = collect($visitors)->merge(collect($bookedServices))->sortBy('activity_date')->values()->toArray();

            return $this->respondOk([
                'status'   => true,
                'data'   => $activities
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function upcomingActivities()
    {
        try {
            $user = auth()->user();

            $today = Carbon::today();

            // Assuming the user has building_id and unit_id
            $buildingId = $user->building_id;
            $unitId = $user->unit_id;

            // $visitors = $user->visitors()->whereStatus('approved')->whereDate('visit_date', '>', $today)
            //     ->select('id', 'visit_date', 'name', 'visitor_type', 'phone_number', 'visitor_note')
            //     ->get()

            $visitors = Visitor::whereIn('status', ['approved', 'in'])
                ->whereDate('visit_date', '>',  $today)
                ->where(function ($query) use ($user, $buildingId, $unitId) {
                    // Visitors created by the user
                    $user->visitors()
                        // OR visitors linked to the same building and unit of the user
                        ->orWhereHas('user', function ($subQuery) use ($buildingId, $unitId) {
                            $subQuery->where('building_id', $buildingId)
                                ->where('unit_id', $unitId);
                        });
                })
                ->select('id', 'visit_date', 'name', 'visitor_type', 'phone_number', 'visitor_note')
                ->get()
                ->map(function ($visitor) {
                    $name = $visitor->name;
                    $visitorType = $visitor->visitor_type;
                    return [
                        'id'            => $visitor->id,
                        'name'          => $visitorType . ' | ' . $name,
                        'type'          => 'visitor',
                        'activity_type' => 'PRE APPROVED',
                        'visitor_type'  => $visitorType,
                        'visitor_phone'  => $visitor->phone_number ?? '',
                        'visitor_note'   => $visitor->visitor_note ?? '',
                        'visitor_other_info'   => $visitor->other_info ?? '',
                        'activity_date'  => $visitor->visit_date->format(config('constant.date_format.date_time')),
                        // 'activity_time' => $visitor->visit_date->format(config('constant.date_format.time')),
                    ];
                });

            $bookedServices = $user->bookedServices()->whereDate('booking_date', '>', $today)
                ->select('id', 'service_id', 'booking_date')
                ->get()
                ->map(function ($bookedService) {
                    return [
                        'id'            => $bookedService->id,
                        'name'          => $bookedService->service->title ?? '',
                        'type'          => 'service_booking',
                        'activity_type' => 'SERVICE BOOKED',
                        'activity_date' => $bookedService->booking_date->format('Y-m-d')
                    ];
                });
            // $dailyHelps = $user->dailyHelps()->whereDate('created_at', $today)->get();

            // $combinedData = $visitors->merge($bookedServices)->merge($dailyHelps)->sortBy('created_at');
            $activities = collect($visitors)->merge(collect($bookedServices))->sortBy('activity_date')->values()->toArray();

            return $this->respondOk([
                'status'   => true,
                'data'   => $activities
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function deleteActivity(Request $request)
    {
        $rules = [];
        $rules['type'] = ['required'];

        if ($request->type == 'visitor') {
            $rules['id'] = ['required', 'exists:visitors,id'];
        }
        $request->validate($rules);

        DB::beginTransaction();
        try {
            $type = $request->type;
            if ($type == 'visitor') {
                $visitor = Visitor::findOrFail($request->id);

                if ($visitor->gatepass_qr_image) {
                    $uploadImageId = $visitor->visitorQr->id;
                    deleteFile($uploadImageId);
                }
                $visitor->delete();
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.deleted_successfully')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }
}
