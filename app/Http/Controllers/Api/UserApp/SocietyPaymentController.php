<?php

namespace App\Http\Controllers\Api\UserApp;

use App\Http\Controllers\Api\APIController;
use App\Models\DeliveryManagement;
use App\Models\DeliveryType;
use App\Models\PaymentRequest;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SocietyPaymentController extends APIController
{

    public function index()
    {
        try {
            $user = auth()->user();
            $payments = PaymentRequest::where('society_id', $user->society_id)
                ->where('building_id', $user->building_id)
                ->where('unit_id', $user->unit_id)
                ->latest()->get()
                ->map(function ($payment) {
                    // if()
                    return [
                        'id' => $payment->id,
                        'title' => $payment->title,
                        'amount' => $payment->amount ?? '',
                        'due_date' => $payment->due_date->format(config('constant.date_format.date')),
                        'paid_at' => $payment->paid_at ? $payment->paid_at->format(config('constant.date_format.date_time')) : '',

                        'created_by' => $payment->createdBy ? $payment->createdBy->name : '',
                        'status' => $payment->status,
                    ];
                });

            return $this->respondOk([
                'status'   => true,
                'data'   => $payments
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function paymentTransaction(Request $request)
    {
        $request->validate([
            'payment_request_id' => ['required', 'exists:payment_requests,id'],
            'status' => ['required', 'in:paid,failed'],
            'amount' => ['required'],
            'payment_data' => ['required'],
        ]);
        DB::beginTransaction();
        try {
            $user = auth()->user();

            $paymentRequest = PaymentRequest::find($request->payment_request_id);

            Transaction::create([
                'user_id' => $user->id,
                'model_id' => $request->payment_request_id,
                'amount' => $request->amount,
                'transaction_type' => 'payment_request',
                'stripe_payment_id' => $request->stripe_payment_id,
                'status'       => $request->status == 'paid' ? 'completed' : 'failed',
                'payment_data' => $request->payment_data,
                'user_data' => $user,
                'model_data' => $paymentRequest
            ]);

            $updatePaymentRequestData = [
                'status' => $request->status
            ];
            if ($request->status == 'paid') {
                $updatePaymentRequestData['paid_at'] = now()->format('Y-m-d H:i:s');
            }

            $paymentRequest->update($updatePaymentRequestData);

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

    public function paymentsDue()
    {
        try {
            $duePayments = DeliveryManagement::whereHas('type', function ($query) {
                $query->where('due_payment', 0)->where('notify_user', 'resident');
            })->get();

            return $this->respondOk([
                'status'   => true,
                'data'   => $duePayments
            ]);
        } catch (\Exception $e) {
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }
}
