<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\MaintenanceItemDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\MaintenanceItem\StoreRequest;
use App\Http\Requests\MaintenanceItem\UpdateRequest;
use App\Models\Category;
use App\Models\MaintenanceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;



class MaintenanceItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MaintenanceItemDataTable $dataTable)
    {
        abort_if(Gate::denies('maintenance_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.maintenance-item.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('maintenance_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $categories = Category::whereStatus(1)->latest()->get();
            return view('backend.maintenance-item.create', compact('categories'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('maintenance_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();
                $maintenanceItem = MaintenanceItem::create($validatedData);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => $maintenanceItem,
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
        abort_if(Gate::denies('maintenance_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $maintenanceItem = MaintenanceItem::where('uuid', $uuid)->first();
            $categories = Category::whereStatus(1)->latest()->get();
            return view('backend.maintenance-item.edit', compact('maintenanceItem', 'categories'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $uuid)
    {
        abort_if(Gate::denies('maintenance_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();
                $maintenanceItem = MaintenanceItem::where('uuid', $uuid)->first();
                $maintenanceItem->update($validatedData);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => $maintenanceItem,
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
        abort_if(Gate::denies('maintenance_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $maintenanceItem = MaintenanceItem::where('uuid', $uuid)->first();
            DB::beginTransaction();
            try {
                if ($maintenanceItem) {
                    $maintenanceItem->delete();
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
}
