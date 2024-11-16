<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\VisitorDataTable;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Visitor\StoreRequest;
use App\Models\Society;
use App\Models\User;
use App\Models\Visitor;
use App\Notifications\UserActivityNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;



class VisitorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(VisitorDataTable $dataTable)
    {
        abort_if(Gate::denies('visitor_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.visitor.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('visitor_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();
            $models = [
                'societies' => $user->is_sub_admin ? $user->societies : Society::whereStatus(1)->latest()->get(),
                'type' => 'create',
            ];
            return view('backend.visitor.create', compact('models'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     * please correct the below code so i create everything perfectly.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $user = Auth::user();
                $residents = User::whereHas('roles', function ($query) {
                    $query->where('id', config('constant.roles.resident'));
                })
                    ->where('society_id', $request->society_id)
                    ->where('building_id', $request->building_id)
                    ->where('unit_id', $request->unit_id)
                    ->get();

                if ($residents->count() == 0) {
                    return response()->json(['success' => false, 'error' => trans('messages.no_resident_found_in_unit')], 500);
                }

                $validatedData = $request->validated();

                if ($request->visitor_type == 'delivery_man' && $request->has('keep_package')) {
                    $validatedData['keep_package'] = 1;
                } else {
                    $validatedData['keep_package'] = 0;
                }

                $validatedData['user_id'] = $residents[0]->id;
                $validatedData['status'] = 'pending';

                if (in_array($validatedData['visitor_type'], ['guest', 'cab', 'delivery_man', 'service_man'])) {
                    do {
                        $gatepass_code = rand(100000, 999999);
                        $checkGatepassCodeExist = Visitor::where('gatepass_code', $gatepass_code)->first();
                    } while ($checkGatepassCodeExist);
                    $visitorInput =  [
                        'visitor_type'  =>  $validatedData['visitor_type'] ?? '',
                        'name'          =>  $validatedData['name'] ?? '',
                        'phone_number'  =>  $validatedData['phone_number'] ?? '',
                        'cab_number'    =>  $validatedData['cab_number'] ?? null,
                        'visitor_note'  =>  $validatedData['visitor_note'] ?? null,
                        'other_info'    =>  $validatedData['other_info'] ?? null,
                        'visit_date'    =>  now() ?? null,
                        'user_id'       =>  $validatedData['user_id'] ?? null,
                        'status'        =>  $validatedData['status'] ?? null,
                        'keep_package'  =>  $validatedData['keep_package'] ?? null,
                        'gatepass_code' =>  $gatepass_code  ?? null,
                        'qr_code'       =>  QrCode::size(300)->generate('Embed this content into the QR Code')  ?? null,
                    ];

                    $visitor = Visitor::create($visitorInput);
                }

                if ($residents->count() > 0) {
                    // Send Notification
                    $visitorType = config('constant.visitor_types')[$request->visitor_type];
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
                    SendPushNotification($userTokens, $title, $notificationMessage, 'user' /* , $visitorDetails */);
                }

                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => $visitor,
                    'message' => trans('messages.crud.add_record')
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        abort_if(Gate::denies('visitor_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            try {
                $visitor = Visitor::where('uuid', $id)->first();
                $viewHTML = view('backend.visitor.show', compact('visitor'))->render();
                return response()->json(array('success' => true, 'htmlView' => $viewHTML));
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $uuid)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $uuid)
    {
        abort_if(Gate::denies('visitor_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $visitor = Visitor::where('uuid', $uuid)->first();
                if ($visitor) {
                    $visitor->delete();
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

    public function visitorStatusChange(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:visitors,uuid',
                'status' => 'required|in:pending,approved,rejected,in,out',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()->toArray(), 'message' => 'Error Occurred!',], 400);
            }

            DB::beginTransaction();
            try {
                $visitor = Visitor::where('uuid', $request->id)->first();
                if ($visitor) {
                    $visitor->update(['status' => $request->status]);
                    $user = Auth::user();
                    $status = $request->status;
                    $userId = $visitor->user_id;
                    $message = trans('messages.notification_messages.visitor_log.status_message', ['status' => $status]);

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
            'module'    => 'visitor'
        ];

        // Notify residents and guards based on user role
        $roles = [
            'resident' => config('constant.roles.resident'),
            'guard' => config('constant.roles.guard')
        ];

        $societyResidents = User::whereStatus(1)->where('id', $userId)->where('id', '!=', $user->id)
            ->whereHas('roles', function ($q) use ($roles) {
                $q->whereIn('id', [$roles['resident']]);
            })
            ->get();

        $societyGuards = User::whereStatus(1)->where('id', $userId)->where('id', '!=', $user->id)
            ->whereHas('roles', function ($q) use ($roles) {
                $q->whereIn('id', [$roles['guard']]);
            })
            ->get();

        // Send notifications
        if ($societyResidents->isNotEmpty()) {
            Notification::send($societyResidents, new UserActivityNotification($notificationData));
            $societyResidentIds = $societyResidents->pluck('id');
            SendPushNotification($societyResidentIds, $title, $message, 'user');
        }

        if ($societyGuards->isNotEmpty()) {
            Notification::send($societyGuards, new UserActivityNotification($notificationData));

            $societyGuardIds = $societyGuards->pluck('id');
            SendPushNotification($societyGuardIds, $title, $message, 'guard');
        }
    }
}
