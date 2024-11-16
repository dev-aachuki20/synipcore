<?php

namespace App\Http\Controllers\Backend;

use Symfony\Component\HttpFoundation\Response;
use App\DataTables\ServiceDataTable;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Service\StoreRequest;
use App\Http\Requests\Service\UpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ServiceDataTable $dataTable)
    {
        abort_if(Gate::denies('service_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.service.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('service_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();

            $providers = User::whereHas('roles', function ($query) {
                $query->where('id', config('constant.roles.provider'));
            });

            if ($user->is_sub_admin) {
                $providers = $providers->where('society_id', $user->society_id);
            }
            $providers = $providers->get();

            $serviceCategories = ServiceCategory::whereStatus(1)->latest()->get();

            return view('backend.service.create', compact('providers', 'user', 'serviceCategories'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('service_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $input = $request->validated();
                $store = Service::create($input);

                if ($store && $request->hasFile('image')) {
                    uploadImage($store, $request->file('image'), 'service/service-images', "service_image", 'original', 'save', null);
                }
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
        abort_if(Gate::denies('service_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
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
    public function edit(Request $request, $id)
    {
        abort_if(Gate::denies('service_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $service = Service::where('uuid', $id)->first();
            $providers = User::whereHas('roles', function ($query) {
                $query->where('id', config('constant.roles.provider'));
            });

            if ($user->is_sub_admin) {
                $providers = $providers->where('society_id', $user->society_id);
            }

            $providers = $providers->get();

            $serviceCategories = ServiceCategory::whereStatus(1)->latest()->get();
            if ($service) {
                return view('backend.service.edit', compact('service', 'providers', 'user', 'serviceCategories'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        abort_if(Gate::denies('service_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $input = $request->validated();

                $service = Service::where('uuid', $input['id'])->first();
                $service->update($input);

                if ($service && $request->hasFile('image')) {
                    $uploadImageId = $service->serviceImage ? $service->serviceImage->id : null;
                    uploadImage($service, $request->file('image'), 'service/service-images', "service_image", 'original', $service->serviceImage ? 'update' : 'save', $uploadImageId ? $uploadImageId : null);
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
    public function destroy(Request $request, $id)
    {
        abort_if(Gate::denies('service_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $service = Service::where('uuid', $id)->first();
                if ($service) {
                    $service->delete();
                    if ($service->serviceImage) {
                        deleteFile($service->serviceImage->id);
                    }
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

    public function createServiceSlug(Request $request)
    {
        if ($request->ajax()) {
            try {
                if ($request->has('title') && !empty($request->input('title'))) {
                    $slug = convertTitleToSlug($request->input('title'));
                    $checkSlug = Service::where('slug', $slug)->first();

                    if ($checkSlug) {
                        $response['success']    = false;
                        $response['data_type']  = 'already_exist';
                        $response['data']       = $slug;
                        $response['message']    = trans('messages.service.slug');
                    } else {
                        $response['success']    = true;
                        $response['data_type']  = 'not_exist';
                        $response['data']       = $slug;
                        $response['message']    = 'Slug Created!';
                    }
                }
                return response()->json($response);
            } catch (\Throwable $th) {
                // \Log::error($th->getMessage().' '.$th->getFile().' '.$th->getLine());
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
    }
}
