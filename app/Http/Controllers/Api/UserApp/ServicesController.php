<?php

namespace App\Http\Controllers\Api\UserApp;

use App\Http\Controllers\Api\APIController;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Notifications\UserActivityNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ServicesController extends APIController
{
    public function index()
    {
        try {
            $servicesList = Service::whereStatus(1)
                ->select('id', 'title')
                ->get()
                ->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'title' => $service->title,
                        'service_image' => $service->service_image_url,
                    ];
                });

            return $this->respondOk([
                'status'   => true,
                'data'   => $servicesList
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public  function serviceDetail($id)
    {
        try {
            $serviceDetail = Service::whereStatus(1)
                ->where('id', $id)
                ->select('id', 'title', 'user_id', 'slug', 'description', 'service_category_id','service_url')
                ->first();

            if (!$serviceDetail) {
                return $this->setStatusCode(404)->respondWithError('Service not found');
            }

            $serviceData = $serviceDetail->toArray();
            // $serviceData['title'] = $serviceDetail->title ?? '';
            // $serviceData['slug'] = $serviceDetail->slug ?? '';
            // $serviceData['description'] = $serviceDetail->description ?? '';
            
            $serviceData['service_category_name'] = $serviceDetail->serviceCategory->title ?? '';
            $serviceData['service_image_url'] = $serviceDetail->service_image_url ?? '';
            $serviceData['provider_id'] = $serviceDetail->provider->id ?? '';
            $serviceData['provider_name'] = $serviceDetail->provider->name ?? '';
            $serviceData['provider_email'] = $serviceDetail->provider->email ?? '';
            $serviceData['provider_mobile_number'] = $serviceDetail->provider->mobile_number ?? '';
            $serviceData['provider_url'] = $serviceDetail->provider->provider_url ?? '';
            $serviceData['profile_image_url'] = $serviceDetail->provider->profile_image_url ?? '';
            $serviceData['provider_description'] = $serviceDetail->provider->description ?? '';
            $serviceData['society_id'] = $serviceDetail->provider->society_id ?? '';
            $serviceData['society_name'] = $serviceDetail->provider->society->name ?? '';
            
            // $serviceData['building_id'] = $serviceDetail->provider->building->id ?? '';
            // $serviceData['building_name'] = $serviceDetail->provider->building->name ?? '';
            // $serviceData['unit_id'] = $serviceDetail->provider->unit->id ?? '';
            // $serviceData['unit_name'] = $serviceDetail->provider->unit->name ?? '';


            return $this->respondOk([
                'status'   => true,
                'data'   => $serviceData
            ]);
        } catch (\Exception $e) {
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function serviceBooking(Request $request)
    {
        $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'description' => ['required'],
            'booking_date' => ['required'],
            'booking_time' => ['required']
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $input = $request->all();

            $bookingDate = Carbon::createFromFormat('d-m-Y', $input['booking_date']);
            $bookingTime = Carbon::createFromFormat('h:i A', $input['booking_time']);

            $input['booking_date'] = $bookingDate->format('Y-m-d');
            $input['booking_time'] = $bookingTime->format('H:i:s');

            $input['user_id'] = $user->id;

            ServiceBooking::create($input);

            // Send Notification to Admin
            $service = Service::find($request->service_id);
            $admins = getUserForNotification(['admin'], $user->society, true);
            if ($admins->count() > 0) {
                $notificationData = [
                    'sender_id' => $user->id,
                    'message'   => trans('messages.notification_messages.service.book_by_user', ['user_name' => $user->name, 'service_name' => $service->title]),
                    'module'    => 'service'
                ];
                Notification::send($admins, new UserActivityNotification($notificationData));
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.crud.add_record')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function bookedServices()
    {
        try {
            $user = auth()->user();

            $bookedServices = $user->bookedServices()
                ->select('id', 'description', 'booking_date', 'booking_time', 'user_id', 'service_id', 'status')
                ->get()
                ->map(function ($bookedService) {
                    return [
                        'id' => $bookedService->id,
                        'description'   => $bookedService->description,
                        'booking_date'  => $bookedService->booking_date->format(config('constant.date_format.date')),
                        'booking_time'  => $bookedService->booking_time->format(config('constant.date_format.time')),

                        'service_name'  => $bookedService->service ? $bookedService->service->title : '',
                        'service_image' => $bookedService->service ? $bookedService->service->service_image_url : '',

                        'status'  => $bookedService->status,
                    ];
                });

            return $this->respondOk([
                'status'   => true,
                'data'   => $bookedServices
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }
}
