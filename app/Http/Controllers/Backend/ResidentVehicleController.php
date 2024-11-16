<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\PreApprovedVehicleDataTable;
use App\DataTables\ResidentVehicleDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResidentVehicle\StoreRequest;
use App\Http\Requests\ResidentVehicle\UpdateRequest;
use App\Models\Building;
use App\Models\ResidentVehicle;
use App\Models\Society;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;



class ResidentVehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ResidentVehicleDataTable $dataTable)
    {
        abort_if(Gate::denies('resident_vehicle_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.resident-vehicle.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('resident_vehicle_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();
            $models = [
                'societies' => $user->is_sub_admin ? $user->societies : Society::whereStatus(1)->latest()->get(),
            ];
            return view('backend.resident-vehicle.create', compact('models'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('resident_vehicle_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();
                ResidentVehicle::create($validatedData);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => trans('messages.created_successfully'),
                ], 201);
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
        abort_if(Gate::denies('resident_vehicle_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            $vehicle = ResidentVehicle::where('uuid', $uuid)->first();
            if (!$vehicle) {
                return response()->json(['success' => false, 'error_type' => 'not_found', 'error' => trans('messages.not_found')], 404);
            }
            $user = auth()->user();

            $models = [
                'societies' => $user->is_sub_admin ? $user->societies : Society::whereStatus(1)->latest()->get(),
                'buildings' => Building::where('society_id', $vehicle->society_id)->whereStatus(1)->latest()->get(),
                'units' => Unit::where('society_id', $vehicle->society_id)->whereStatus(1)->latest()->get(),
            ];

            return view('backend.resident-vehicle.edit', compact('vehicle', 'models'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $uuid)
    {
        abort_if(Gate::denies('resident_vehicle_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();
                $vehicle = ResidentVehicle::where('uuid', $uuid)->first();
                if ($vehicle) {
                    $vehicle->update($validatedData);
                }
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => trans('messages.updated_successfully'),
                ], 201);
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
        abort_if(Gate::denies('resident_vehicle_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $residentVehicle = ResidentVehicle::where('uuid', $uuid)->first();
            DB::beginTransaction();
            try {
                if ($residentVehicle) {
                    $residentVehicle->delete();
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

    public function preApprovedVehicle(PreApprovedVehicleDataTable $dataTable)
    {
        abort_if(Gate::denies('resident_pre_approved_vehicle_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.resident-vehicle.pre-approved');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    public function vehicleStatusChange(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:resident_vehicles,uuid',
                'status' => 'required|in:pending,approved,rejected',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()->toArray(), 'message' => 'Error Occurred!',], 400);
            }

            DB::beginTransaction();
            try {
                $residentVehicle = ResidentVehicle::where('uuid', $request->id)->update(['status' => $request->status]);

                DB::commit();
                return response()->json(['success'    => true, 'message'   => trans('messages.status_update_successfully'),]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
    }

    public function filterLocation(Request $request)
    {
        try {
            if ($request->ajax()) {
                $id = $request['id'] ?? 0;
                if (isset($request['type']) && !empty($request['type']) && ($id > 0)) {
                    $html = '';
                    switch ($request['type']) {
                        case 'get_building_by_society':
                            $buildings = Building::whereStatus(1)->where('society_id', $id)->orderBy('id', 'desc')->get();

                            $html = '<option value="">' . trans('global.select') . ' ' . trans('cruds.resident_vehicle.fields.building') . '</option>';
                            $html .= $buildings->map(function ($building) {
                                return "<option value=" . $building->id . " data-building_id=" . $building->id . ">" . $building->title . "</option>";
                            })->implode('');
                            break;

                        case 'get_unit_by_building':
                            $units = Unit::whereStatus(1)->where('building_id', $id)->latest()->get();

                            $html = '<option value="">' . trans('global.select') . ' ' . trans('cruds.resident_vehicle.fields.unit') . '</option>';
                            $html .= $units->map(function ($unit) {
                                return "<option value=" . $unit->id . ">" . $unit->title . "</option>";
                            })->implode('');
                            break;
                        default:
                            break;
                    }
                    return response()->json(['success' => true, 'message' => trans('messages.data_retrieved_successfully'), 'data' => $html]);
                }
            }
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }
}
