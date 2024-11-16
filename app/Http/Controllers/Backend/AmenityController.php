<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\AmenityBookingDataTable;
use App\DataTables\AmenityDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Amenity\StoreRequest;
use App\Http\Requests\Amenity\UpdateRequest;
use App\Models\Amenity;
use App\Models\AmenityBooking;
use App\Models\Society;
use App\Models\User;
use App\Notifications\UserActivityNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;


class AmenityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(AmenityDataTable $dataTable)
    {
        abort_if(Gate::denies('amenity_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.amenity.index');
        } catch (\Exception $e) {

            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('amenity_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();
            $societies = Society::whereStatus(1)->latest()->pluck('name', 'uuid')->toArray();
            return view('backend.amenity.create', compact('societies', 'user'));
        } catch (\Exception $e) {

            return abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('amenity_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $input = $request->validated();
                $input['fee_type']  = !empty($request->fee) ? 'paid' : 'free';

                $society = Society::where('uuid', $request->society_id)->select('id')->first();

                if ($society) {
                    $input['society_id']  = $society->id;
                }


                Amenity::create($input);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => trans('messages.created_successfully'),
                ], 201);
            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 500);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $uuid)
    {
        abort_if(Gate::denies('amenity_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();
            $amenity = Amenity::where('uuid', $uuid)->first();
            if (!$amenity) {
                return abort(500);
            }
            $societies = Society::whereStatus(1)->latest()->pluck('name', 'uuid')->toArray();
            return view('backend.amenity.edit', compact('societies', 'amenity', 'user'));
        } catch (\Exception $e) {

            return abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $uuid)
    {
        abort_if(Gate::denies('amenity_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $amenity = Amenity::where('uuid', $uuid)->first();
                if (!$amenity) {
                    return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 500);
                }

                $input = $request->validated();
                $input['fee_type']  = !empty($request->fee) ? 'paid' : 'free';

                $society = Society::where('uuid', $request->society_id)->select('id')->first();

                if ($society) {
                    $input['society_id']  = $society->id;
                }


                $amenity->update($input);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => trans('messages.updated_successfully'),
                ], 201);
            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 500);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $uuid)
    {
        abort_if(Gate::denies('amenity_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $amenity = Amenity::where('uuid', $uuid)->first();

                if ($amenity->amenityBookings()->exists()) {
                    return response()->json(['success' => false, 'error' => trans('messages.amenity.delete_error_booking_exist')], 400);
                }
                if ($amenity) {
                    $amenity->delete();
                }

                DB::commit();
                return response()->json([
                    'success'    => true,
                    'message'    => trans('messages.deleted_successfully'),
                ]);
            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }

    public function amenityBookings(AmenityBookingDataTable $dataTable)
    {
        abort_if(Gate::denies('amenity_booking_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.amenity-booking.index');
        } catch (\Exception $e) {

            return abort(500);
        }
    }

    public function amenityStatusChange(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:amenity_bookings,uuid',
                'status' => 'required|in:pending,approved,rejected',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()->toArray(), 'message' => 'Error Occurred!',], 400);
            }

            DB::beginTransaction();
            try {
                $amenityBooking = AmenityBooking::where('uuid', $request->id)->first();
                if ($amenityBooking) {
                    $amenityBooking->update(['status' => $request->status]);
                    $user = Auth::user();
                    $status = $request->status;
                    $userId = $amenityBooking->user_id;
                    $message = trans('messages.notification_messages.amenity_booking.status_message', ['status' => ucfirst($status)]);
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
            'module'    => 'amenity'
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
