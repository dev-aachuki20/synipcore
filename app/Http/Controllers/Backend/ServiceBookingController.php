<?php

namespace App\Http\Controllers\Backend;

use Symfony\Component\HttpFoundation\Response;
use App\DataTables\ServiceBookingDataTable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\ServiceBooking;
use App\Models\User;
use App\Notifications\UserActivityNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ServiceBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ServiceBookingDataTable $dataTable)
    {
        abort_if(Gate::denies('service_booking_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.service-booking.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        abort_if(Gate::denies('service_booking_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $service = ServiceBooking::where('uuid', $id)->first();
                if ($service) {
                    $service->delete();
                }

                DB::commit();
                return response()->json(['success'    => true, 'message'    => trans('messages.deleted_successfully'),]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }

    public function statusChange(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:service_bookings,uuid',
                'status' => 'required|in:accept,reject,pending',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()->toArray(), 'message' => 'Error Occurred!',], 400);
            }

            DB::beginTransaction();
            try {
                $serviceBooking = ServiceBooking::where('uuid', $request->id)->first();
                if ($serviceBooking) {
                    $serviceBooking->update(['status' => $request->status]);
                    $user = Auth::user();
                    $status = $request->status;
                    $userId = $serviceBooking->user_id;
                    $message = trans('messages.notification_messages.service_booking.status_message', ['status' => $status]);
                    $this->sendNotifications($user, $status, $message, $userId);
                }

                DB::commit();
                return response()->json(['success'    => true, 'message'   => trans('messages.status_update_successfully'),]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
    }

    protected function sendNotifications($user, $status, $message, $userId)
    {
        $title = '';

        // Prepare notification data
        $notificationData = [
            'sender_id' => $user->id,
            'message'   => $message,
            'module'    => 'servicebooking'
        ];

        // Notify residents and guards based on user role
        $roles = [
            'resident' => config('constant.roles.resident'),
        ];

        $societyResidents = User::whereStatus(1)->where('id', $userId)->where('id', '!=', $user->id)
            ->whereHas('roles', function ($q) use ($roles) {
                $q->whereIn('id', [$roles['resident']]);
            })
            ->get();

        // Send notifications
        if ($societyResidents->isNotEmpty()) {
            Notification::send($societyResidents, new UserActivityNotification($notificationData));
            $societyResidentIds = $societyResidents->pluck('id');
            SendPushNotification($societyResidentIds, $title, $message, 'user');
        }
    }
}
