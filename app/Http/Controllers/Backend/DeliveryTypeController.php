<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\DeliveryTypeDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeliveryType\StoreRequest;
use App\Http\Requests\DeliveryType\UpdateRequest;
use App\Models\DeliveryType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;



class DeliveryTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(DeliveryTypeDataTable $dataTable)
    {
        abort_if(Gate::denies('delivery_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.delivery-type.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('delivery_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return view('backend.delivery-type.create');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('delivery_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();
                $deliveryType = DeliveryType::create($validatedData);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => $deliveryType,
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
        abort_if(Gate::denies('delivery_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $deliveryType = DeliveryType::where('uuid', $uuid)->first();
            return view('backend.delivery-type.edit', compact('deliveryType'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $uuid)
    {
        abort_if(Gate::denies('delivery_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();
                $deliveryType = DeliveryType::where('uuid', $uuid)->first();
                $deliveryType->update($validatedData);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => $deliveryType,
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
        abort_if(Gate::denies('delivery_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $deliveryType = DeliveryType::where('uuid', $uuid)->first();
            DB::beginTransaction();
            try {
                if ($deliveryType) {
                    $deliveryType->delete();
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
