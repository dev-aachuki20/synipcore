<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\DeliveryManagementDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeliveryManagement\StoreRequest;
use App\Http\Requests\DeliveryManagement\UpdateRequest;
use App\Models\Building;
use App\Models\DeliveryManagement;
use App\Models\DeliveryType;
use App\Models\Society;
use App\Models\Unit;
use App\Models\User;
use App\Notifications\UserActivityNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Notification;




class DeliveryManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(DeliveryManagementDataTable $dataTable)
    {
        abort_if(Gate::denies('delivery_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.delivery-management.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('delivery_management_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();

            $types = DeliveryType::whereStatus(1)->latest()->pluck('title', 'id')->toArray();
            $societies = Society::whereStatus(1)->latest()->pluck('name', 'uuid', 'id')->toArray();
            $buildings  = Building::whereStatus(1)->where('society_id', $user->society_id)->latest()->pluck('title', 'uuid')->toArray();

            return view('backend.delivery-management.create', compact('types', 'societies', 'buildings', 'user'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('delivery_management_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();
                $society = Society::where('uuid', $request->society_id)->select('id')->first();
                if ($society) {
                    $validatedData['society_id']  = $society->id;
                }

                $building = Building::where('uuid', $request->building_id)->select('id')->first();
                if ($building) {
                    $validatedData['building_id']  = $building->id;
                }

                $unit = Unit::where('uuid', $request->unit_id)->select('id')->first();
                if ($unit) {
                    $validatedData['unit_id']  = $unit->id;
                }

                $validatedData['respondant_id'] = Auth::user()->id;

                $deliveryManagement = DeliveryManagement::create($validatedData);

                // Get the role(s) to notify based on the delivery type
                $notifyUserRole      = DeliveryType::where('id', $request->delivery_type_id)->pluck('notify_user')->first();

                // Send notifications to the filtered users
                $user = Auth::user();
                $title = $request->subject;
                $message = $validatedData['message'] ?? '';
                $this->sendNotifications($deliveryManagement->id, $user, $title, $message, $notifyUserRole, $society->id, $building->id ?? null, $unit->id ?? null);


                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => $deliveryManagement,
                    'message' => trans('messages.created_successfully'),
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $uuid)
    {
        abort_if(Gate::denies('delivery_management_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();

            $deliveryManagement = DeliveryManagement::where('uuid', $uuid)->first();
            if (!$deliveryManagement) {
                return abort(500);
            }
            $types = DeliveryType::whereStatus(1)->latest()->pluck('title', 'id')->toArray();
            $societies  = Society::whereStatus(1)->latest()->pluck('name', 'uuid', 'id')->toArray();
            $buildings  = Building::where('society_id', $deliveryManagement->society_id)->whereStatus(1)->latest()->pluck('title', 'uuid')->toArray();
            $units      = Unit::where('building_id', $deliveryManagement->building_id)->whereStatus(1)->latest()->pluck('title', 'uuid')->toArray();
            return view('backend.delivery-management.edit', compact('types', 'deliveryManagement', 'societies', 'units', 'buildings', 'user'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $uuid)
    {
        abort_if(Gate::denies('delivery_management_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $deliveryManagement = DeliveryManagement::where('uuid', $uuid)->first();
                if (!$deliveryManagement) {
                    return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 500);
                }
                $validatedData = $request->validated();

                $society = Society::where('uuid', $request->society_id)->select('id')->first();
                if ($society) {
                    $validatedData['society_id']  = $society->id;
                }

                $building = Building::where('uuid', $request->building_id)->select('id')->first();
                if ($building) {
                    $validatedData['building_id']  = $building->id;
                }

                $unit = Unit::where('uuid', $request->unit_id)->select('id')->first();
                if ($unit) {
                    $validatedData['unit_id']  = $unit->id;
                }

                $validatedData['respondant_id'] = Auth::user()->id;

                $deliveryManagement->update($validatedData);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => $deliveryManagement,
                    'message' =>  trans('messages.updated_successfully'),
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $uuid)
    {
        abort_if(Gate::denies('delivery_management_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $deliveryManagement = DeliveryManagement::where('uuid', $uuid)->first();
                if ($deliveryManagement) {
                    $deliveryManagement->delete();
                }
                DB::commit();
                $response = [
                    'success'    => true,
                    'message'    => trans('messages.deleted_successfully'),
                ];
                return response()->json($response);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }

    protected function sendNotifications($moduleId, $user, $title, $message, $role, $societyId, $buildingId = null, $unitId = null)
    {
        // Prepare notification data
        $notificationData = [
            'sender_id' => $user->id,
            'title'     => $title,
            'message'   => $message,
            'module'    => 'delivery_management',
            'module_id' => $moduleId
        ];

        // Notify residents and guards based on user role
        $roles = [
            'resident' => config('constant.roles.resident'),
            'guard' => config('constant.roles.guard'),
            'admin' => config('constant.roles.admin'),
        ];
        // Initialize an empty collection for users to notify

        $guardResidents = User::whereStatus(1)->where('id', '!=', $user->id)
            ->where('society_id', $societyId)
            ->when($buildingId, function ($query, $buildingId) {
                return $query->where('building_id', $buildingId);
            })
            ->when($unitId, function ($query, $unitId) {
                return $query->where('unit_id', $unitId);
            })
            ->whereHas('roles', function ($q) use ($roles) {
                $q->whereIn('id', [$roles['guard']]);
            })
            ->get();


        $societyResidents = User::whereStatus(1)->where('id', '!=', $user->id)
            ->where('society_id', $societyId)
            ->when($buildingId, function ($query, $buildingId) {
                return $query->where('building_id', $buildingId);
            })
            ->when($unitId, function ($query, $unitId) {
                return $query->where('unit_id', $unitId);
            })
            ->whereHas('roles', function ($q) use ($roles) {
                $q->whereIn('id', [$roles['resident']]);
            })
            ->get();



        $societyAdmins = User::whereStatus(1)->where('id', '!=', $user->id)
            ->where('society_id', $societyId)
            ->whereHas('roles', function ($q) use ($roles) {
                $q->whereIn('id', [$roles['admin']]);
            })
            ->get();

        if ($role == 'guard') {
            // Send notifications to guards
            if ($guardResidents->isNotEmpty()) {
                Notification::send($guardResidents, new UserActivityNotification($notificationData));
                $guardResidentsIds = $guardResidents->pluck('id');
                SendPushNotification($guardResidentsIds, $title, $message, 'guard');
            }
        } elseif ($role == 'resident') {
            // Send notifications to resident
            if ($societyResidents->isNotEmpty()) {
                Notification::send($societyResidents, new UserActivityNotification($notificationData));
                $societyResidentIds = $societyResidents->pluck('id');
                SendPushNotification($societyResidentIds, $title, $message, 'user');
            }
        } elseif ($role == 'admin') {
            // Send notifications to sub admin
            if ($societyAdmins->isNotEmpty()) {
                Notification::send($societyAdmins, new UserActivityNotification($notificationData));
            }
        }
    }

    public function getNotifyUserRole(Request $request)
    {
        $notifyUserRole = DeliveryType::where('id', $request->delivery_type_id)->pluck('notify_user')->first();

        return response()->json([
            'success' => true,
            'notifyUserRole' => $notifyUserRole,
        ], 200);
    }
}
