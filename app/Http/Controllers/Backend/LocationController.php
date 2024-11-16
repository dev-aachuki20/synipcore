<?php

namespace App\Http\Controllers\Backend;

use Symfony\Component\HttpFoundation\Response;
use App\DataTables\LocationDataTable;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Location\StoreRequest;
use App\Http\Requests\Location\UpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\MetaField;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(LocationDataTable $dataTable)
    {
        abort_if(Gate::denies('location_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try{
            return $dataTable->render('backend.location.index');
        }catch (\Exception $e) {     
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('location_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try{         
            $parentLocation = Location::whereStatus(1)->latest()->get();       
            return view('backend.location.create', compact('parentLocation'));  
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('location_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) 
        {
            DB::beginTransaction();
            try{
                $input = $request->validated();
                $parentId = Location::where('uuid', $input['parent_id'])->pluck('id')->first();
                $input['parent_id'] = $parentId;
                $store = Location::create($input);
    
                if ($store && $request->hasFile('image')) {
                    uploadImage($store, $request->file('image'), 'location/location-images', "location_image", 'original', 'save', null);
                }

                if ($store && $request->has('key') && $request->has('value')) {
                    $keys = $request->input('key');
                    $values = $request->input('value');

                    if (count($keys) === count($values)) {
                        $metaFields = array_map(function ($key, $value) use ($store) {
                            return new MetaField([
                                'key'           => $key,
                                'value'         => $value,
                                'metaable_id'   => $store->id,
                                'metaable_type' => get_class($store),
                            ]);
                        }, $keys, $values);
    
                        $store->metafields()->saveMany($metaFields);
                    }
                }
                DB::commit();
    
                return response()->json([
                    'success' => true,
                    'message' => trans('messages.created_successfully'),
                ], 201);
            } catch (\Exception $e) {
                DB::rollBack();         
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort_if(Gate::denies('location_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($request->ajax()) {
            try{                
                
            } 
            catch (\Exception $e) {
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        abort_if(Gate::denies('location_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try{
            $location = Location::with(['metafieldKeysValue'])->where('uuid',$id)->first();
            $parentLocation = Location::whereStatus(1)->latest()->get();
            if($location){
                return view('backend.location.edit', compact('location', 'parentLocation'));  
            }
        } catch (\Exception $e) {
            DB::rollBack();         
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        abort_if(Gate::denies('location_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        if ($request->ajax()) 
        {
            DB::beginTransaction();
            try {
                $input = $request->validated();
                $parentId = Location::where('uuid', $input['parent_id'])->pluck('id')->first();
                $input['parent_id'] = $parentId ?? null;
                $location = Location::where('uuid', $input['id'])->first();
                $location->update($input);
                                
                if($location && $request->hasFile('image')){
                    $uploadImageId = $location->locationImage ? $location->locationImage->id : null;
                    uploadImage($location, $request->file('image'), 'location/location-images', "location_image", 'original', $location->locationImage ? 'update' : 'save', $uploadImageId ? $uploadImageId : null);
                }
                
                if ($location && $request->has('key') && $request->has('value')) {
                    $keys = $request->input('key');
                    $values = $request->input('value');

                    if (count($keys) === count($values)) {
                        $metaFields = array_map(function ($key, $value) use ($location) {
                            return new MetaField([
                                'key'           => $key,
                                'value'         => $value,
                                'metaable_id'   => $location->id,
                                'metaable_type' => get_class($location),
                            ]);
                        }, $keys, $values);

                        $location->metafields()->delete();
                        $location->metafields()->saveMany($metaFields);
                    }
                }
                
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => trans('messages.updated_successfully'),
                ], 201);

            } catch (\Exception $e) {
                DB::rollBack();                
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        abort_if(Gate::denies('location_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $location = Location::where('uuid', $id)->first();
                if ($location) {
                    $location->delete();
                    if ($location->locationImage) {
                        deleteFile($location->locationImage->id);
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

    public function createLocationSlug(Request $request)
    {
        if ($request->ajax()) {
            try {
                if ($request->has('title') && !empty($request->input('title'))) {
                    $slug = convertTitleToSlug($request->input('title'));
                    $checkSlug = Location::where('slug', $slug)->first();
                    
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
            } catch (\Throwable $th) {
                // \Log::error($th->getMessage().' '.$th->getFile().' '.$th->getLine());
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
    }


}
