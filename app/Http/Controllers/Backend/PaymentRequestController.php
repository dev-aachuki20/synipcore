<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\PaymentRequestDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest\StoreRequest;
use App\Http\Requests\PaymentRequest\UpdateRequest;
use App\Models\Building;
use App\Models\PaymentRequest;
use App\Models\Society;
use App\Models\Unit;
use App\Models\User;
use App\Notifications\UserActivityNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;


class PaymentRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PaymentRequestDataTable $dataTable)
    {
        abort_if(Gate::denies('payment_request_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.payment-request.index');
        } catch (\Exception $e) {
           
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('payment_request_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();
            $societies = Society::whereStatus(1)->latest()->pluck('name', 'uuid')->toArray();
            $buildings  = Building::whereStatus(1)->where('society_id', $user->society_id)->latest()->pluck('title', 'uuid')->toArray();
            $paymentRequestStatuses = config('constant.payment_request_status');
            return view('backend.payment-request.create', compact('societies', 'paymentRequestStatuses', 'user', 'buildings'));
        } catch (\Exception $e) {
           
            return abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('payment_request_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $input = $request->validated();

                $input['due_date']  = date('Y-m-d', strtotime($request->due_date));

                $society = Society::where('uuid', $request->society_id)->select('id')->first();
                if ($society) {
                    $input['society_id']  = $society->id;
                }

                $building = Building::where('uuid', $request->building_id)->select('id')->first();
                if ($building) {
                    $input['building_id']  = $building->id;
                }

                $unit = Unit::where('uuid', $request->unit_id)->select('id')->first();
                if ($unit) {
                    $input['unit_id']  = $unit->id;
                }

                PaymentRequest::create($input);

                $user = Auth::user();
                $title = $request->title;
                $unit = $request->unit;
                $message = trans('messages.notification_messages.payment_request.request');

                $this->sendNotifications($user, $title, $message, $unit);

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
        abort_if(Gate::denies('payment_request_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();
            $paymentRequest = PaymentRequest::where('uuid', $uuid)->first();
            if (!$paymentRequest) {
                return abort(500);
            }
            $societies  = Society::whereStatus(1)->latest()->pluck('name', 'uuid')->toArray();
            $buildings  = Building::whereStatus(1)->where('society_id', $user->society_id)->latest()->pluck('title', 'uuid')->toArray();
            $units      = Unit::whereStatus(1)->where('society_id', $user->society_id)->latest()->pluck('title', 'uuid')->toArray();

            $paymentRequestStatuses = config('constant.payment_request_status');

            return view('backend.payment-request.edit', compact('societies', 'units', 'buildings', 'paymentRequest', 'paymentRequestStatuses', 'user'));
        } catch (\Exception $e) {
           
            return abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $uuid)
    {
        abort_if(Gate::denies('payment_request_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $paymentRequest = PaymentRequest::where('uuid', $uuid)->first();
                if (!$paymentRequest) {
                    return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 500);
                }

                $input = $request->validated();
                $input['due_date']  = date('Y-m-d', strtotime($request->due_date));

                $society = Society::where('uuid', $request->society_id)->select('id')->first();
                if ($society) {
                    $input['society_id']  = $society->id;
                }

                $building = Building::where('uuid', $request->building_id)->select('id')->first();
                if ($building) {
                    $input['building_id']  = $building->id;
                }

                $unit = Unit::where('uuid', $request->unit_id)->select('id')->first();
                if ($unit) {
                    $input['unit_id']  = $unit->id;
                }


                $paymentRequest->update($input);

                $user = Auth::user();
                $title = $request->title;
                $unit = $request->unit;
                $message = trans('messages.notification_messages.payment_request.request');

                $this->sendNotifications($user, $title, $message, $unit);

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
        abort_if(Gate::denies('payment_request_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $paymentRequest = PaymentRequest::where('uuid', $uuid)->first();

                if ($paymentRequest) {
                    $paymentRequest->delete();
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

    protected function sendNotifications($user, $title, $message, $unit)
    {
        $title = ucwords(str_replace('_', ' ', $title));

        // Prepare notification data
        $notificationData = [
            'sender_id' => $user->id,
            'message'   => $message,
            'module'    => 'payment_request'
        ];

        // Notify residents and guards based on user role
        $roles = [
            'resident' => config('constant.roles.resident'),
            // 'guard' => config('constant.roles.guard')
        ];

        $societyResidents = User::whereStatus(1)->where('unit_id', $unit)->where('id', '!=', $user->id)
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
