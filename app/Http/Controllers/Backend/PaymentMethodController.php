<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\PaymentMethodDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentMethod\StoreRequest;
use App\Http\Requests\PaymentMethod\UpdateRequest;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;



class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PaymentMethodDataTable $dataTable)
    {
        abort_if(Gate::denies('payment_method_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.payment-method.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('payment_method_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return view('backend.payment-method.create');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('payment_method_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();
                $paymentMethod = PaymentMethod::create($validatedData);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => $paymentMethod,
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
        abort_if(Gate::denies('payment_method_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $paymentMethod = PaymentMethod::where('uuid', $uuid)->first();
            return view('backend.payment-method.edit', compact('paymentMethod'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $uuid)
    {
        abort_if(Gate::denies('payment_method_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();
                $paymentMethod = PaymentMethod::where('uuid', $uuid)->first();
                $paymentMethod->update($validatedData);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => $paymentMethod,
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
        abort_if(Gate::denies('payment_method_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $paymentMethod = PaymentMethod::where('uuid', $uuid)->first();
            DB::beginTransaction();
            try {
                $paymentMethod->delete();
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

    public function createPaymentMethodSlug(Request $request)
    {
        if ($request->ajax()) {
            try {
                if ($request->has('title') && !empty($request->input('title'))) {
                    $slug = convertTitleToSlug($request->input('title'));
                    $checkSlug = PaymentMethod::where('slug', $slug)->first();

                    if ($checkSlug) {
                        $response['success']    = false;
                        $response['data_type']  = 'already_exist';
                        $response['data']       = $slug;
                        $response['message']    = trans('messages.location.slug');
                    } else {
                        $response['success']    = true;
                        $response['data_type']  = 'not_exist';
                        $response['data']       = $slug;
                        $response['message']    = 'Slug Created!';
                    }
                }
                return response()->json($response);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
    }
}
