<?php

namespace App\Http\Controllers\Backend;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\DataTables\ResidentDataTable;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resident\StoreRequest;
use App\Http\Requests\Resident\UpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Society;
use App\Models\Building;
use App\Models\Unit;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ResidentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ResidentDataTable $dataTable)
    {
        abort_if(Gate::denies('resident_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.resident.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('resident_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();

            $models = [
                'societies' => $user->is_sub_admin ? $user->societies : Society::whereStatus(1)->latest()->get(),
                'type' => 'create',
            ];

            return view('backend.resident.create', compact('models'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        dd($request->all());
        abort_if(Gate::denies('resident_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $input = $request->validated();
                $input['resident_type'] = $input['type'];

                $user = User::create($input);
                $user->roles()->sync([config('constant.roles.resident')]);

                do {
                    $securityPin = rand(100000, 999999);
                } while (User::where('security_pin', $securityPin)->exists());

                $qrcode = QrCode::size(300)->generate('Embed this content into the QR Code');
                $user->qr_code_path = $qrcode;
                $user->security_pin = $securityPin;
                $user->save();

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
        abort_if(Gate::denies('resident_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            try {
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
        abort_if(Gate::denies('resident_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            $resident = User::where('uuid', $uuid)->first();
            if (!$resident) {
                return response()->json(['success' => false, 'error_type' => 'not_found', 'error' => trans('messages.not_found')], 404);
            }
            $user = auth()->user();

            $models = [
                'societies' => $user->is_sub_admin ? $user->societies : Society::whereStatus(1)->latest()->get(),
                'buildings' => Building::where('society_id', $resident->society_id)->whereStatus(1)->latest()->get(),
                'units' => Unit::where('society_id', $resident->society_id)->whereStatus(1)->latest()->get(),
                'type' => 'edit'
            ];

            return view('backend.resident.edit', compact('resident', 'models'));
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
        abort_if(Gate::denies('resident_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $input = $request->validated();
                $input['resident_type'] = $input['type'];
                $resident = User::where('uuid', $uuid)->first();
                if (!$resident) {
                    throw new \Exception('User not found');
                }
                $resident->update($input);

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
        abort_if(Gate::denies('resident_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $resident = User::where('uuid', $uuid)->first();
                if ($resident) {
                    $resident->delete();
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

    public function filterResidentData(Request $request)
    {
        try {
            if ($request->ajax()) {
                $id = $request['id'] ?? 0;
                if (isset($request['type']) && !empty($request['type']) && ($id > 0)) {
                    $html = '';
                    switch ($request['type']) {
                        case 'get_building_by_society':
                            $buildings = Building::whereStatus(1)->where('society_id', $id)->latest()->get();

                            $html = '<option value="">' . trans('global.select') . ' ' . trans('cruds.resident.fields.building') . '</option>';
                            $html .= $buildings->map(function ($building) {
                                return "<option value=" . $building->id . " data-building_id=" . $building->id . ">" . $building->title . "</option>";
                            })->implode('');
                            break;

                        case 'get_unit_by_building':
                            $units = Unit::whereStatus(1)->where('building_id', $id)->latest()->get();

                            $html = '<option value="">' . trans('global.select') . ' ' . trans('cruds.resident.fields.unit') . '</option>';
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
        }
    }

    public function isVerified(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:users,uuid',
            ]);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()->toArray(), 'message' => 'Error Occurred!',], 400);
            }

            DB::beginTransaction();
            try {
                $user = User::where('uuid', $request->id)->firstOrFail();
                $verified = isset($user) && ($user->is_verified == 0) ? 1 : 0;
                $user->update(['is_verified' => $verified]);

                DB::commit();
                return response()->json(['success'    => true, 'message'   => trans('messages.status_update_successfully'),]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
    }
}
