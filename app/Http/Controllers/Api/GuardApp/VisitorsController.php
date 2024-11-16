<?php

namespace App\Http\Controllers\Api\GuardApp;

use App\Http\Controllers\Api\APIController;
use App\Models\Building;
use App\Models\ResidentDailyHelp;
use App\Models\ResidentFamilyMember;
use App\Models\ResidentVehicle;
use App\Models\Unit;
use App\Models\User;
use App\Models\Visitor;
use App\Models\VisitorLog;
use App\Notifications\UserActivityNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class VisitorsController extends APIController
{
    public function preApprovedVisitor(Request $request)
    {
        $request->validate([
            'gatepass_code' => ['required' /*, 'exists:visitors,gatepass_code' */],
            'visitor_type'  => ['required']
        ]);
        try {
            // $visitor = Visitor::where('gatepass_code', $request->gatepass_code)->first();

            $visitor_type = $request->visitor_type;
            if ($visitor_type == 'guest') {
                $visitor = Visitor::where('gatepass_code', $request->gatepass_code)->where('visitor_type', $visitor_type)->first();
            } elseif ($visitor_type == 'cab') {
                $visitor = Visitor::where('gatepass_code', $request->gatepass_code)->where('visitor_type', $visitor_type)->first();
            } elseif ($visitor_type == 'delivery_man') {
                $visitor = Visitor::where('gatepass_code', $request->gatepass_code)->where('visitor_type', $visitor_type)->first();
            } elseif ($visitor_type == 'service_man') {
                $visitor = Visitor::where('gatepass_code', $request->gatepass_code)->where('visitor_type', $visitor_type)->first();
            } elseif ($visitor_type == 'family_member') {
                $visitor = ResidentFamilyMember::where('gatepass_code', $request->gatepass_code)->first();
            } elseif ($visitor_type == 'daily_help') {
                $visitor = ResidentDailyHelp::where('gatepass_code', $request->gatepass_code)->first();
            } elseif ($visitor_type == 'vehicle') {
                $visitor = ResidentVehicle::where('gatepass_code', $request->gatepass_code)->first();
            }

            // If no matching record is found, return an error response
            if (!$visitor) {
                return $this->setStatusCode(404)->respondWithError('No record found for the provided gatepass code.');
            }

            return $this->respond([
                'status'            => true,
                'data'              => [
                    'id'                => $visitor->id,
                    'uuid'              => $visitor->uuid,
                    'name'              => $visitor->name,
                    'mobile_number'     => $visitor->phone_number ?? '',
                    'visitor_type'      => $visitor->visitor_type ?? $visitor_type,
                    'gatepass_code'     => $visitor->gatepass_code ?? '',
                    'user_id'           => $visitor->user_id ?? '',
                    'user_name'         => $visitor->user->name ?? '',
                    'user_mobile_number' => $visitor->user->mobile_number ?? '',
                    'building'          => $visitor->user->building->title ?? '',
                    'unit'              => $visitor->user->unit->title ?? '',
                    'other_info'        => $visitor->other_info ?? '',

                ]
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function inVisitorList()
    {
        try {
            // $today = Carbon::today();
            // $daysAgo = Carbon::today()->subDays(14);

            // $visitors = Visitor::whereBetween('visit_date', [$daysAgo, $today])->whereStatus('in')

            $visitors = Visitor::whereStatus('in')
                ->select('id', 'user_id', 'visit_date', 'name', 'visitor_type', 'phone_number', 'visitor_note', 'status', 'created_at')
                ->latest()
                ->get()
                ->map(function ($visitor) {
                    return [
                        'id'            => $visitor->id,
                        'visitor_name'  => $visitor->name ?? '',
                        'visitor_type'  => $visitor->visitor_type ? ucwords(str_replace('_', ' ', $visitor->visitor_type)) : '',
                        'visitor_phone' => $visitor->phone_number ?? '',
                        'visitor_note'  => $visitor->visitor_note ?? '',
                        'visitor_other_info'    => $visitor->other_info ?? '',
                        'visitor_status'  => $visitor->status ?? '',

                        'resident_id'       => $visitor->user->id ?? '',
                        'resident_name'     => $visitor->user->name ?? '',
                        'resident_society_id' => $visitor->user->society->id ?? '',
                        'resident_society' => $visitor->user->society->name ?? '',
                        'resident_building_id' => $visitor->user->building->id ?? '',
                        'resident_building' => $visitor->user->building->title ?? '',
                        'resident_unit_id'     => $visitor->user->unit->id ?? '',
                        'resident_unit'     => $visitor->user->unit->title ?? '',
                        'created_at'        => $visitor->created_at->format(config('constant.date_format.date_time')) ?? '',
                        'visit_date'        => $visitor->visit_date->format(config('constant.date_format.date_time')) ?? '',
                    ];
                });


            // Fetch Resident Vehicle
            $residentVehicles = ResidentVehicle::whereStatus('in')
                ->select('id', 'resident_id', 'vehicle_number', 'vehicle_type', 'vehicle_model', 'vehicle_color', 'parking_slot_no', 'society_id', 'building_id', 'unit_id', 'status', 'created_at')
                ->latest()
                ->get()
                ->map(function ($residentVehicle) {
                    return [
                        'id'                    => $residentVehicle->id,
                        'visitor_type'          => 'Vehicle' ??  '',
                        'vehicle_number'        => $residentVehicle->vehicle_number,
                        'vehicle_type'          => $residentVehicle->vehicle_type,
                        'vehicle_model'         => $residentVehicle->vehicle_model,
                        'vehicle_color'         => $residentVehicle->vehicle_color,
                        'parking_slot_no'       => $residentVehicle->parking_slot_no,
                        'visitor_status'        => $residentVehicle->status ?? '',
                        'resident_id'           => $residentVehicle->user->id ?? '',
                        'resident_name'         => $residentVehicle->user->name ?? '',
                        'resident_society_id'   => $residentVehicle->user->society->id ?? '',
                        'resident_society'      => $residentVehicle->user->society->name ?? '',
                        'resident_building_id'  => $residentVehicle->user->building->id ?? '',
                        'resident_building'     => $residentVehicle->user->building->title ?? '',
                        'resident_unit_id'      => $residentVehicle->user->unit->id ?? '',
                        'resident_unit'         => $residentVehicle->user->unit->title ?? '',
                        'created_at'            => $residentVehicle->created_at->format(config('constant.date_format.date_time')) ?? '',
                    ];
                });

            // Fetch Family member
            $residentFamilyMembers = ResidentFamilyMember::whereStatus('in')
                ->select('id', 'resident_id', 'name', 'phone_number', 'relation', 'status', 'created_at')
                ->latest()
                ->get()
                ->map(function ($residentFamilyMember) {
                    return [
                        'id'                    => $residentFamilyMember->id,
                        'visitor_type'          => 'Family Member' ??  '',
                        'name'                  => $residentFamilyMember->name,
                        'phone_number'          => $residentFamilyMember->phone_number,
                        'relation'              => $residentFamilyMember->relation,
                        'visitor_status'        => $residentFamilyMember->status ?? '',
                        'resident_id'           => $residentFamilyMember->resident->id ?? '',
                        'resident_name'         => $residentFamilyMember->resident->name ?? '',
                        'resident_society_id'   => $residentFamilyMember->resident->society->id ?? '',
                        'resident_society'      => $residentFamilyMember->resident->society->name ?? '',
                        'resident_building_id'  => $residentFamilyMember->resident->building->id ?? '',
                        'resident_building'     => $residentFamilyMember->resident->building->title ?? '',
                        'resident_unit_id'      => $residentFamilyMember->resident->unit->id ?? '',
                        'resident_unit'         => $residentFamilyMember->resident->unit->title ?? '',
                        'created_at'            => $residentFamilyMember->created_at->format(config('constant.date_format.date_time')) ?? '',
                    ];
                });

            // Fetch daily help
            $residentDailyHelps = ResidentDailyHelp::whereStatus('in')
                ->select('id', 'resident_id', 'name', 'phone_number', 'help_type', 'society_id', 'building_id', 'unit_id', 'status', 'created_at')
                ->latest()
                ->get()
                ->map(function ($residentDailyHelp) {
                    return [
                        'id'                    => $residentDailyHelp->id,
                        'visitor_type'          => 'Daily Help' ??  '',
                        'name'                  => $residentDailyHelp->name ?? '',
                        'phone_number'          => $residentDailyHelp->phone_number ?? '',
                        'help_type'             => $residentDailyHelp->help_type ?? '',
                        'visitor_status'        => $residentDailyHelp->status ?? '',
                        'resident_id'           => $residentDailyHelp->resident->id ?? '',
                        'resident_name'         => $residentDailyHelp->resident->name ?? '',
                        'resident_society_id'   => $residentDailyHelp->resident->society->id ?? '',
                        'resident_society'      => $residentDailyHelp->resident->society->name ?? '',
                        'resident_building_id'  => $residentDailyHelp->resident->building->id ?? '',
                        'resident_building'     => $residentDailyHelp->resident->building->title ?? '',
                        'resident_unit_id'      => $residentDailyHelp->resident->unit->id ?? '',
                        'resident_unit'         => $residentDailyHelp->resident->unit->title ?? '',
                        'created_at'            => $residentDailyHelp->created_at->format(config('constant.date_format.date_time')) ?? '',
                    ];
                });


            // Combine both visitor and family member lists
            $combinedVisitors = $visitors->concat($residentVehicles);
            $combinedFamilyMember = $combinedVisitors->concat($residentFamilyMembers);
            $combinedDailyHelps = $combinedFamilyMember->concat($residentDailyHelps);


            return $this->respondOk([
                'status'   => true,
                'data'   => $combinedDailyHelps
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function waitingVisitorList()
    {
        try {
            $today = Carbon::today();
            // $sevenDaysAgo = Carbon::today()->subDays(14);

            $twentyFourHoursAgo = Carbon::now()->subHours(24);  // Time 24 hours ago
            $now = Carbon::now(); // current time

            $visitorsWithLastOutStatus = Visitor::whereBetween('visit_date', [$twentyFourHoursAgo, $now])->whereIn('status', ['approved', 'pending'])
                ->select('id', 'user_id', 'visit_date', 'name', 'visitor_type', 'phone_number', 'visitor_note', 'status', 'created_at')
                ->latest()
                ->get()
                ->map(function ($visitor) {
                    return [
                        'id'            => $visitor->id,
                        'visitor_name'  => $visitor->name ?? '',
                        'visitor_type'  => $visitor->visitor_type ? ucwords(str_replace('_', ' ', $visitor->visitor_type)) : '',
                        'visitor_phone' => $visitor->phone_number ?? '',
                        'visitor_note'  => $visitor->visitor_note ?? '',
                        'visitor_status'  => $visitor->status ?? '',
                        'visitor_other_info'    => $visitor->other_info ?? '',

                        'resident_id'       => $visitor->user->id ?? '',
                        'resident_name'     => $visitor->user->name ?? '',
                        'resident_society_id' => $visitor->user->society->id ?? '',
                        'resident_society' => $visitor->user->society->name ?? '',
                        'resident_building_id' => $visitor->user->building->id ?? '',
                        'resident_building' => $visitor->user->building->title ?? '',
                        'resident_unit_id'     => $visitor->user->unit->id ?? '',
                        'resident_unit'     => $visitor->user->unit->title ?? '',

                        'is_waiting' => $visitor->status == 'pending' ? true : false,

                        'created_at'        => $visitor->created_at->format(config('constant.date_format.date_time')) ?? '',
                        'visit_date'        => $visitor->visit_date->format(config('constant.date_format.date_time')) ?? '',
                    ];
                });

            return $this->respondOk([
                'status' => true,
                'data'   => $visitorsWithLastOutStatus
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function outVisitorList()
    {
        try {
            $today = Carbon::today();
            $sevenDaysAgo = Carbon::today()->subDays(14);

            $visitorsWithLastOutStatus = Visitor::whereBetween('visit_date', [$sevenDaysAgo, $today])->where('status', 'out')
                ->select('id', 'user_id', 'visit_date', 'name', 'visitor_type', 'phone_number', 'visitor_note', 'status', 'created_at')
                ->latest()
                ->get()
                ->map(function ($visitor) {
                    return [
                        'id'            => $visitor->id,
                        'visitor_name'  => $visitor->name ?? '',
                        'visitor_type'  => $visitor->visitor_type ? ucwords(str_replace('_', ' ', $visitor->visitor_type)) : '',
                        'visitor_phone' => $visitor->phone_number ?? '',
                        'visitor_note'  => $visitor->visitor_note ?? '',
                        'visitor_status'  => $visitor->status ?? '',
                        'visitor_other_info'    => $visitor->other_info ?? '',

                        'resident_id'       => $visitor->user->id ?? '',
                        'resident_name'     => $visitor->user->name ?? '',
                        'resident_society_id' => $visitor->user->society->id ?? '',
                        'resident_society' => $visitor->user->society->name ?? '',
                        'resident_building_id' => $visitor->user->building->id ?? '',
                        'resident_building' => $visitor->user->building->title ?? '',
                        'resident_unit_id'     => $visitor->user->unit->id ?? '',
                        'resident_unit'     => $visitor->user->unit->title ?? '',

                        'is_waiting' => $visitor->status == 'pending' ? true : false,

                        'created_at'        => $visitor->created_at->format(config('constant.date_format.date_time')) ?? '',
                        'visit_date'        => $visitor->visit_date->format(config('constant.date_format.date_time')) ?? '',
                    ];
                });

            // Fetch Resident Vehicle
            // $residentVehicles = ResidentVehicle::whereBetween('visit_date', [$sevenDaysAgo, $today])->where('status', 'out')
            //     ->select('id', 'resident_id', 'vehicle_number', 'vehicle_type', 'vehicle_model', 'vehicle_color', 'parking_slot_no', 'society_id', 'building_id', 'unit_id', 'status', 'created_at')
            //     ->latest()
            //     ->get()
            //     ->map(function ($residentVehicle) {
            //         return [
            //             'id'                    => $residentVehicle->id,
            //             'visitor_type'          => 'Vehicle' ??  '',
            //             'vehicle_number'        => $residentVehicle->vehicle_number,
            //             'vehicle_type'          => $residentVehicle->vehicle_type,
            //             'vehicle_model'         => $residentVehicle->vehicle_model,
            //             'vehicle_color'         => $residentVehicle->vehicle_color,
            //             'parking_slot_no'       => $residentVehicle->parking_slot_no,
            //             'visitor_status'        => $residentVehicle->status ?? '',
            //             'resident_id'           => $residentVehicle->user->id ?? '',
            //             'resident_name'         => $residentVehicle->user->name ?? '',
            //             'resident_society_id'   => $residentVehicle->user->society->id ?? '',
            //             'resident_society'      => $residentVehicle->user->society->name ?? '',
            //             'resident_building_id'  => $residentVehicle->user->building->id ?? '',
            //             'resident_building'     => $residentVehicle->user->building->title ?? '',
            //             'resident_unit_id'      => $residentVehicle->user->unit->id ?? '',
            //             'resident_unit'         => $residentVehicle->user->unit->title ?? '',
            //             'created_at'            => $residentVehicle->created_at->format(config('constant.date_format.date_time')) ?? '',
            //         ];
            //     });

            // // Fetch Family member
            // $residentFamilyMembers = ResidentFamilyMember::whereBetween('visit_date', [$sevenDaysAgo, $today])->where('status', 'out')
            //     ->select('id', 'resident_id', 'name', 'phone_number', 'relation', 'status', 'created_at')
            //     ->latest()
            //     ->get()
            //     ->map(function ($residentFamilyMember) {
            //         return [
            //             'id'                    => $residentFamilyMember->id,
            //             'visitor_type'          => 'Family Member' ??  '',
            //             'name'                  => $residentFamilyMember->name,
            //             'phone_number'          => $residentFamilyMember->phone_number,
            //             'relation'              => $residentFamilyMember->relation,
            //             'visitor_status'        => $residentFamilyMember->status ?? '',
            //             'resident_id'           => $residentFamilyMember->resident->id ?? '',
            //             'resident_name'         => $residentFamilyMember->resident->name ?? '',
            //             'resident_society_id'   => $residentFamilyMember->resident->society->id ?? '',
            //             'resident_society'      => $residentFamilyMember->resident->society->name ?? '',
            //             'resident_building_id'  => $residentFamilyMember->resident->building->id ?? '',
            //             'resident_building'     => $residentFamilyMember->resident->building->title ?? '',
            //             'resident_unit_id'      => $residentFamilyMember->resident->unit->id ?? '',
            //             'resident_unit'         => $residentFamilyMember->resident->unit->title ?? '',
            //             'created_at'            => $residentFamilyMember->created_at->format(config('constant.date_format.date_time')) ?? '',
            //         ];
            //     });

            // // Fetch daily help
            // $residentDailyHelps = ResidentDailyHelp::whereBetween('visit_date', [$sevenDaysAgo, $today])->where('status', 'out')
            //     ->select('id', 'resident_id', 'name', 'phone_number', 'help_type', 'society_id', 'building_id', 'unit_id', 'status', 'created_at')
            //     ->latest()
            //     ->get()
            //     ->map(function ($residentDailyHelp) {
            //         return [
            //             'id'                    => $residentDailyHelp->id,
            //             'visitor_type'          => 'Daily Help' ??  '',
            //             'name'                  => $residentDailyHelp->name ?? '',
            //             'phone_number'          => $residentDailyHelp->phone_number ?? '',
            //             'help_type'             => $residentDailyHelp->help_type ?? '',
            //             'visitor_status'        => $residentDailyHelp->status ?? '',
            //             'resident_id'           => $residentDailyHelp->resident->id ?? '',
            //             'resident_name'         => $residentDailyHelp->resident->name ?? '',
            //             'resident_society_id'   => $residentDailyHelp->resident->society->id ?? '',
            //             'resident_society'      => $residentDailyHelp->resident->society->name ?? '',
            //             'resident_building_id'  => $residentDailyHelp->resident->building->id ?? '',
            //             'resident_building'     => $residentDailyHelp->resident->building->title ?? '',
            //             'resident_unit_id'      => $residentDailyHelp->resident->unit->id ?? '',
            //             'resident_unit'         => $residentDailyHelp->resident->unit->title ?? '',
            //             'created_at'            => $residentDailyHelp->created_at->format(config('constant.date_format.date_time')) ?? '',
            //         ];
            //     });


            // // Combine both visitor and family member lists
            // $combinedVisitors = $visitorsWithLastOutStatus->concat($residentVehicles);
            // $combinedFamilyMember = $combinedVisitors->concat($residentFamilyMembers);
            // $combinedDailyHelps = $combinedFamilyMember->concat($residentDailyHelps);


            return $this->respondOk([
                'status' => true,
                'data'   => $visitorsWithLastOutStatus
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function inOutVisitorStatus(Request $request)
    {
        $request->validate(
            [
                'visitor_id'    => ['required', 'exists:visitors,id'],
                'type'          => ['required', 'in:in,out']
            ],
            [],
            [
                'visitor_id'    => trans('validation.attributes.visitor_id'),
                'type'          => trans('validation.attributes.type')
            ]
        );

        DB::beginTransaction();
        try {
            $visitor = Visitor::where('id', $request->visitor_id)->first();
            $visitorLog = new VisitorLog();
            $visitorLog->visitor_logsable_type = get_class($visitor);
            $visitorLog->visitor_logsable_id = $visitor->id;
            $visitorLog->status = $request->type;

            // Save the VisitorLog to the database
            $visitorLog->save();

            // update visitor status
            $visitor->update(['status' => $request->type]);

            $visitorData = [
                'visitor_id' => $visitor->id,
                'status' => $request->type,
            ];

            DB::commit();

            return $this->respondOk([
                'status'   => true,
                'message'  => trans('messages.login_success'),
                'data'     => $visitorData
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function addVisitor(Request $request)
    {
        $request->validate([
            'type'          => ['required', 'in:' . implode(',', array_keys(config('constant.visitor_types')))],
            'name'          => ['required'],
            'phone_number'  => ['nullable', 'required_if:type,guest', 'required_if:type,service_man', 'numeric', 'digits_between:10,15'],
            'cab_number'    => ['nullable', 'required_if:type,cab', 'min:4', 'max:4'],
            // 'visit_date'    => ['nullable', 'required_if:type,guest', 'required_if:type,service_man', 'required_if:type,delivery_man'],

            'building_id'   => ['required', 'exists:buildings,id'],
            'unit_id'       => ['required', 'exists:units,id'],
            'visitor_note'  => ['nullable'],
            'other_info'    => ['nullable'],
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $residents = User::whereHas('roles', function ($query) {
                $query->where('id', config('constant.roles.resident'));
            })
                ->where('society_id', $user->society_id)
                ->where('building_id', $request->building_id)
                ->where('unit_id', $request->unit_id)
                ->get();

            if ($residents->count() == 0) {
                return $this->setStatusCode(500)->respondWithError(trans('messages.no_resident_found_in_unit'));
            }

            $input = $request->all();
            // $input['visit_date'] = now()->format('Y-m-d');
            $input['visit_date'] = now();

            if ($request->type == 'delivery_man' && $request->has('keep_package')) {
                $input['keep_package'] = 1;
            }

            $input['visitor_type'] = $request->type;
            $input['user_id'] = $residents[0]->id;
            $input['status'] = 'pending';

            $visitor = Visitor::create($input);

            $building = Building::find($request->building_id);
            $unit = Unit::find($request->unit_id);

            $visitorDetails = [
                'visitor_id'            =>  $visitor->id ?? '',
                'visitor_name'          =>  $visitor->name ?? '',
                'visitor_phone_no'      =>  $visitor->phone_number ?? '',
                'visitor_cab_number'    =>  $visitor->cab_number ?? '',
                'visitor_visit_date'    =>  $visitor->visit_date->format('d M Y, h:i A') ?? '',
                'visitor_type'          =>  config('constant.status_type.visitor_types')[$visitor->visitor_type] ?? 'Visitor',
                'visitor_note'          =>  $visitor->visitor_note ?? '',
                'visitor_gatepass_code' =>  $visitor->gatepass_code ?? '',
                'visitor_other_info' =>  $visitor->other_info ?? '',
                'visitor_status'        =>  $visitor->status ?? '',
                'visitor_building_id'   =>  $building->id ?? '',
                'visitor_building_name' =>  $building->title ?? '',
                'visitor_unit_id'       =>  $unit->id ?? '',
                'visitor_unit_name'     =>  $unit->title ?? '',
            ];

            if ($residents->count() > 0) {
                // Send Notification
                $visitorType = config('constant.visitor_types')[$request->type];
                $title = $request->name;
                $notificationMessage = trans('messages.notification_messages.visitor_on_gate_no_preapproved', ['visitor_type' => $visitorType]);
                $notificationData = [
                    'sender_id' => $visitor->id,
                    'message'   => $notificationMessage,
                    'module'    => 'visitor_request',
                ];
                Notification::send($residents, new UserActivityNotification($notificationData));

                // send Push Notification
                $userTokens = $residents->pluck('id');
                SendPushNotification($userTokens, $title, $notificationMessage, 'user', $visitorDetails);
            }


            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'visitor_id' =>  $visitor->id,
                'resident_phone' =>  $residents[0]->id ?? null,
                'message'   => trans('messages.crud.add_record')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function allowDeclineVisitorByGuard(Request $request)
    {
        $request->validate(
            [
                'visitor_id'    => ['required', 'exists:visitors,id'],
                'status'        => ['required', 'in:approved,rejected'],
            ],
            [],
            [
                'visitor_id'    => trans('validation.attributes.visitor_id'),
            ]
        );

        DB::beginTransaction();
        try {
            $visitor = Visitor::where('id', $request->visitor_id)->first();

            if ($request->status == 'approved') {
                $visitorLog = new VisitorLog();
                $visitorLog->status = 'in';
                $visitor->visitorLogs()->save($visitorLog);
            }

            // update visitor status
            $visitor->update(['status' => $request->status == 'approved' ? 'in' : 'rejected']);

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'  => trans('messages.notification.visitor_status_success'),
                'data'     => $visitor
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function checkVisitorStatusByGuard($visitorId)
    {
        try {
            $visitor = Visitor::where('id', $visitorId)->first();
            if (!$visitor) {
                $status = 'rejected';
            } else {
                $status = $visitor->status;
            }

            return $this->respondOk([
                'status'   => true,
                'visitor_status'  => $status,
                'data'     => $visitor
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function deleteVisitorByGuard($visitorId)
    {
        DB::beginTransaction();
        try {
            $visitor = Visitor::findOrFail($visitorId);
            if (!$visitor) {
                return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
            }

            if ($visitor->gatepass_qr_image) {
                $uploadImageId = $visitor->visitorQr->id;
                deleteFile($uploadImageId);
            }
            $visitor->delete();

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
