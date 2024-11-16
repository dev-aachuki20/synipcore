<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\ServiceCategoryDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceCategory\StoreRequest;
use App\Http\Requests\ServiceCategory\UpdateRequest;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;



class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ServiceCategoryDataTable $dataTable)
    {
        abort_if(Gate::denies('service_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.service-category.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('service_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return view('backend.service-category.create');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('service_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();
                $serviceCategory = ServiceCategory::create($validatedData);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => $serviceCategory,
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
        abort_if(Gate::denies('service_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $serviceCategory = ServiceCategory::where('uuid', $uuid)->first();
            return view('backend.service-category.edit', compact('serviceCategory'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $uuid)
    {
        abort_if(Gate::denies('service_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();
                $serviceCategory = ServiceCategory::where('uuid', $uuid)->first();
                $serviceCategory->update($validatedData);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => $serviceCategory,
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
        abort_if(Gate::denies('service_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $serviceCategory = ServiceCategory::where('uuid', $uuid)->first();
            DB::beginTransaction();
            try {
                $serviceCategory->delete();
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
