<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\ComplaintTypeDataTable;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\ComplaintType\StoreRequest;
use App\Http\Requests\ComplaintType\UpdateRequest;
use App\Models\ComplaintType;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ComplaintTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ComplaintTypeDataTable $dataTable)
    {
        abort_if(Gate::denies('complaint_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.complaint-type.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('complaint_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return view('backend.complaint-type.create');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('complaint_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();

                $complaintType = ComplaintType::create($validatedData);

                if ($request->has('image')) {
                    $uploadId = null;
                    $actionType = 'save';
                    if ($complaintImageRecord = $complaintType->complaintImage) {
                        $uploadId = $complaintImageRecord->id;
                        $actionType = 'update';
                    }
                    uploadImage($complaintType, $request->image, 'complaint/complaintType-images', "complaint_image", 'original', $actionType, $uploadId);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'data' => $complaintType,
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
    public function edit($uuid)
    {
        abort_if(Gate::denies('complaint_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            $complaintType = ComplaintType::where('uuid', $uuid)->first();
            return view('backend.complaint-type.edit', compact('complaintType'));
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
        abort_if(Gate::denies('complaint_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();

                $complaintType = ComplaintType::where('uuid', $uuid)->first();
                $complaintType->update($validatedData);

                if ($request->has('image')) {
                    $uploadId = null;
                    $actionType = 'save';
                    if ($complaintImageRecord = $complaintType->complaintImage) {
                        $uploadId = $complaintImageRecord->id;
                        $actionType = 'update';
                    }
                    uploadImage($complaintType, $request->image, 'complaint/complaintType-images', "complaint_image", 'original', $actionType, $uploadId);
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
        abort_if(Gate::denies('complaint_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $complaintType = ComplaintType::where('uuid', $uuid)->first();
                if ($complaintType) {
                    if ($complaintType->complaint_image_url) {
                        $uploadImageId = $complaintType->complaintImage->id;
                        deleteFile($uploadImageId);
                    }
                    $complaintType->delete();
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

    public function createComplaintSlug(Request $request)
    {
        if ($request->ajax()) {
            try {
                if ($request->has('title') && !empty($request->input('title'))) {
                    $slug = convertTitleToSlug($request->input('title'));
                    $checkSlug = ComplaintType::where('slug', $slug)->first();

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
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
    }
}
