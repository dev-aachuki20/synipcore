<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\CameraDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Camera\StoreRequest;
use App\Http\Requests\Camera\UpdateRequest;
use App\Models\Building;
use App\Models\Camera;
use App\Models\Society;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;


class CameraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CameraDataTable $dataTable)
    {
        abort_if(Gate::denies('camera_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.camera.index');
        } catch (\Exception $e) {

            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('camera_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();
            $societies = Society::whereStatus(1)->latest()->pluck('name', 'uuid')->toArray();
            $buildings  = Building::whereStatus(1)->where('society_id', $user->society_id)->latest()->pluck('title', 'uuid')->toArray();

            return view('backend.camera.create', compact('societies', 'user', 'buildings'));
        } catch (\Exception $e) {

            return abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('camera_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();

                $society = Society::where('uuid', $request->society_id)->select('id')->first();
                $building = Building::where('uuid', $request->building_id)->select('id')->first();
                $unit = Unit::where('uuid', $request->unit_id)->select('id')->first();

                if ($society) {
                    $validatedData['society_id']  = $society->id;
                }

                if ($building) {
                    $validatedData['building_id']  = $building->id;
                }

                if ($unit) {
                    $validatedData['unit_id']  = $unit->id;
                }


                Camera::create($validatedData);

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
        abort_if(Gate::denies('camera_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();
            $camera = Camera::where('uuid', $uuid)->first();
            if (!$camera) {
                return abort(500);
            }
            $societies = Society::whereStatus(1)->latest()->pluck('name', 'uuid')->toArray();
            $buildings  = Building::where('society_id', $camera->society_id)->whereStatus(1)->latest()->pluck('title', 'uuid')->toArray();
            $units      = Unit::where('building_id', $camera->building_id)->whereStatus(1)->latest()->pluck('title', 'uuid')->toArray();
            return view('backend.camera.edit', compact('societies', 'camera', 'user', 'buildings', 'units'));
        } catch (\Exception $e) {

            return abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $uuid)
    {
        abort_if(Gate::denies('camera_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $camera = Camera::where('uuid', $uuid)->first();
                if (!$camera) {
                    return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 500);
                }

                $validatedData = $request->validated();

                $society = Society::where('uuid', $request->society_id)->select('id')->first();

                if ($society) {
                    $validatedData['society_id']  = $society->id;
                }

                $building = Building::where('uuid', $request->building_id)->select('id')->first();
                if ($building) {
                    $validatedData['building_id'] = $building->id;
                }

                $unit = Unit::where('uuid', $request->unit_id)->select('id')->first();
                if ($unit) {
                    $validatedData['unit_id']  = $unit->id;
                }


                $camera->update($validatedData);

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
        abort_if(Gate::denies('camera_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $camera = Camera::where('uuid', $uuid)->first();

                if ($camera) {
                    $camera->delete();
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
}
