<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\FeatureDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Feature\StoreRequest;
use App\Http\Requests\Feature\UpdateRequest;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;



class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FeatureDataTable $dataTable)
    {
        abort_if(Gate::denies('feature_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.feature.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('feature_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return view('backend.feature.create');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('feature_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();
                $feature = Feature::create($validatedData);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => $feature,
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
     * Display the specified resource.T
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
        abort_if(Gate::denies('feature_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $feature = Feature::where('uuid', $uuid)->first();
            return view('backend.feature.edit', compact('feature'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $uuid)
    {
        abort_if(Gate::denies('feature_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();
                $feature = Feature::where('uuid', $uuid)->first();
                $feature->update($validatedData);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => $feature,
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
        abort_if(Gate::denies('feature_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $feature = Feature::where('uuid', $uuid)->first();
            DB::beginTransaction();
            try {
                $feature->delete();
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
}
