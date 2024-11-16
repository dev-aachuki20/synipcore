<?php

namespace App\Http\Controllers\Api\UserApp;

use App\Http\Controllers\Api\APIController;
use App\Models\Amenity;
use App\Models\AmenityBooking;
use App\Models\Transaction;
use App\Notifications\UserActivityNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AmenityController extends APIController
{

    public function index()
    {
        try {
            $user = auth()->user();
            $amenities = Amenity::where('society_id', $user->society_id)
                ->select('id', 'title', 'fee_type')
                ->latest()->get();

            return $this->respondOk([
                'status'   => true,
                'data'   => $amenities
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function amenityDetail($amenity_id)
    {
        try {
            $amenity = Amenity::where('id', $amenity_id)
                ->select('id', 'title', 'description', 'fee_type', 'fee', 'capacity', 'booking_capacity', 'advance_booking_days')
                ->first();

            return $this->respondOk([
                'status'   => true,
                'data'   => $amenity
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function amenityBook(Request $request)
    {
        $request->validate([
            'amenity_id' => ['required', 'exists:amenities,id'],
            'from_date' => ['required'],
            'to_date' => ['required', 'after_or_equal:from_date'],

            'from_time' => ['required'],
            'to_time' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value <= $request->from_time) {
                        $fail('The ' . str_replace('_', ' ', $attribute) . ' must be greater than the from time.');
                    }
                }
            ],
            'is_full_day' => ['required'],
            'max_users_capacity' => ['required', 'integer', 'min:1'],
        ]);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $amenity = Amenity::find($request->amenity_id);

            $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date);
            $fromTime = Carbon::createFromFormat('h:i A', $request->from_time);

            $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date);
            $toTime = Carbon::createFromFormat('h:i A', $request->to_time);

            $currentDate = Carbon::parse(now()->format('Y-m-d'));
            $minimumAmenitybookingTime = $currentDate->addDays($amenity->advance_booking_days);

            if ($minimumAmenitybookingTime->gte($fromDate)) {
                return $this->setStatusCode(422)->respondWithError(trans('messages.amenity.advance_booking_error', ['days' => $amenity->advance_booking_days]));
            }

            $startDate  = $fromDate->format('Y-m-d');
            $startTime  = $fromTime->format('H:i:s');
            $endDate    = $toDate->format('Y-m-d');
            $endTime    = $toTime->format('H:i:s');
            if ($request->is_full_day) {
                $startTime  = $fromTime->startOfDay()->format('H:i:s');
                $endTime    = $toTime->endOfDay()->format('H:i:s');
            }

            $isAmenityExists = isAmenityExists($amenity->id, $startDate, $startTime, $endDate, $endTime);

            if ($isAmenityExists) {
                return $this->setStatusCode(422)->respondWithError(trans('messages.amenity.already_booked'));
            }

            $amenityBooking = AmenityBooking::create([
                'user_id' => $user->id,
                'amenity_id' => $amenity->id,
                'from_date' => $startDate,
                'to_date'   => $endDate,
                'from_time' => $startTime,
                'to_time'   => $endTime,
                'amount'    => $request->has('amount') && !empty($request->amount) ? $request->amount : NULL,
                'is_full_day' => $request->is_full_day ? 1 : 0,
                'max_users_capacity' => $request->max_users_capacity,
                'payment_status' => $amenity->fee_type == 'free' ? 'completed' : 'pending'
            ]);

            // Send Notification to Admin
            $requestedDate = $fromDate->format(config('constant.date_format.date')) . ' to ' . $toDate->format(config('constant.date_format.date'));
            $admins = getUserForNotification(['admin'], $user->society, true);
            if ($admins->count() > 0) {
                $notificationData = [
                    'sender_id' => $user->id,
                    'message'   => trans(
                        'messages.notification_messages.amenity_booking.booking_notification',
                        [
                            'user_name' => $user->name,
                            'amenity_name' => $amenity->title,
                            'requested_date' => $requestedDate
                        ]
                    ),
                    'module'    => 'amenity'
                ];
                Notification::send($admins, new UserActivityNotification($notificationData));
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'is_booked' => $amenity->fee_type == 'free' ? true : false,
                'amenity_booking_id' => $amenityBooking->id,
                'message'   => $amenity->fee_type == 'free' ? trans('messages.amenity.booked_success') : ''
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function paymentTransaction(Request $request)
    {
        $request->validate([
            'amenity_booking_id' => ['required', 'exists:amenity_bookings,id'],
            'status' => ['required', 'in:paid,failed'],
            'amount' => ['required'],
            'payment_data' => ['required'],
        ]);

        DB::beginTransaction();
        try {
            $user = auth()->user();

            $amenityBooking = AmenityBooking::find($request->amenity_booking_id);

            Transaction::create([
                'user_id' => $user->id,
                'model_id' => $request->amenity_booking_id,
                'amount' => $request->amount,
                'transaction_type' => 'amenity_booking',
                'stripe_payment_id' => $request->stripe_payment_id,
                'status'       => $request->status == 'paid' ? 'completed' : 'failed',
                'payment_data' => $request->payment_data,
                'user_data' => $user,
                'model_data' => $amenityBooking
            ]);

            $amenityBooking->update(['payment_status' => $request->status == 'paid' ? 'completed' : 'failed']);

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.payment_request.sucess_payment')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function bookedAmenities()
    {
        try {
            $user = auth()->user();

            $bookedAmenities = $user->bookedAmenities()
                ->select('id', 'amenity_id', 'from_date', 'from_time', 'to_date', 'to_time', 'status', 'created_at', 'is_full_day')
                ->get()
                ->map(function ($bookedAmenity) {
                    $transaction = Transaction::where('transaction_type', 'amenity_booking')->where('model_id', $bookedAmenity->id)->whereStatus('completed')->first();
                    $amount = 'free';
                    if ($transaction) {
                        $amount = $transaction->amount;
                    }
                    return [
                        'id' => $bookedAmenity->id,
                        'amenity_name'   => $bookedAmenity->amenity->title,
                        'from_date'  => $bookedAmenity->from_date->format(config('constant.date_format.date')),
                        'from_time'  => $bookedAmenity->from_time->format(config('constant.date_format.time')),

                        'to_date'  => $bookedAmenity->to_date->format(config('constant.date_format.date')),
                        'to_time'  => $bookedAmenity->to_time->format(config('constant.date_format.time')),

                        'boooking_date'  => $bookedAmenity->created_at->format(config('constant.date_format.date_time')),
                        'is_full_day' => $bookedAmenity->is_full_day,

                        'amount' => $amount,
                        'status'   => $bookedAmenity->status,
                    ];
                });

            return $this->respondOk([
                'status'   => true,
                'data'   => $bookedAmenities
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }
}
