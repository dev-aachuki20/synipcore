<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\PropertyManagementDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyManagement\StoreRequest;
use App\Http\Requests\PropertyManagement\UpdateRequest;
use App\Models\Building;
use App\Models\MaintenanceItem;
use App\Models\PropertyManagement;
use App\Models\PropertyType;
use App\Models\Society;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;


class PropertyManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PropertyManagementDataTable $dataTable)
    {
        abort_if(Gate::denies('property_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.property-management.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('property_management_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();
            $maintenanceItems = MaintenanceItem::whereStatus(1)->latest()->pluck('title', 'id')->toArray();
            $propertyTypes = PropertyType::whereStatus(1)->latest()->get();
            $societies = Society::whereStatus(1)->latest()->pluck('name', 'uuid', 'id')->toArray();
            $buildings = Building::where('society_id', $user->society_id)->whereStatus(1)->latest()->get();
            return view('backend.property-management.create', compact('societies', 'maintenanceItems', 'propertyTypes', 'user', 'buildings'));
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('property_management_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();

                $validatedData['purchase_date']  = date('Y-m-d', strtotime($request->purchase_date));

                $society = Society::where('uuid', $request->society_id)->select('id')->first();
                if ($society) {
                    $validatedData['society_id']  = $society->id;
                }

                $building = Building::where('uuid', $request->building_id)->select('id')->first();
                if ($building) {
                    $validatedData['building_id']  = $building->id;
                }

                $unit = Unit::where('uuid', $request->unit_id)->select('id')->first();
                if ($unit) {
                    $validatedData['unit_id']  = $unit->id;
                }

                $propertyManagement = PropertyManagement::create($validatedData);

                if ($propertyManagement && $request->has('property_image')) {
                    $files = $request->file('property_image');
                    if (is_array($files)) {
                        array_map(function ($file) use ($propertyManagement) {
                            if ($file->isValid()) {
                                uploadImage($propertyManagement, $file, 'property/property-images', 'property_image', 'original', 'save', null);
                            }
                        }, $files);
                    }
                }

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
        abort_if(Gate::denies('property_management_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $propertyManagement = PropertyManagement::where('uuid', $uuid)->first();
            if (!$propertyManagement) {
                return abort(500);
            }
            $maintenanceItems = MaintenanceItem::whereStatus(1)->latest()->pluck('title', 'id')->toArray();
            $propertyTypes = PropertyType::whereStatus(1)->latest()->get();
            $societies  = Society::whereStatus(1)->latest()->pluck('name', 'uuid', 'id')->toArray();
            $buildings  = Building::where('society_id', $propertyManagement->society_id)->whereStatus(1)->latest()->pluck('title', 'uuid')->toArray();
            $units      = Unit::where('building_id', $propertyManagement->building_id)->whereStatus(1)->latest()->pluck('title', 'uuid')->toArray();

            return view('backend.property-management.edit', compact('societies', 'units', 'buildings', 'propertyManagement', 'maintenanceItems', 'propertyTypes'));
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $uuid)
    {
        abort_if(Gate::denies('property_management_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $propertyManagement = PropertyManagement::where('uuid', $uuid)->first();
                if (!$propertyManagement) {
                    return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 500);
                }

                $validatedData = $request->validated();
                $validatedData['purchase_date']  = date('Y-m-d', strtotime($request->purchase_date));

                $society = Society::where('uuid', $request->society_id)->select('id')->first();
                if ($society) {
                    $validatedData['society_id']  = $society->id;
                }

                $building = Building::where('uuid', $request->building_id)->select('id')->first();
                if ($building) {
                    $validatedData['building_id']  = $building->id;
                }

                $unit = Unit::where('uuid', $request->unit_id)->select('id')->first();
                if ($unit) {
                    $validatedData['unit_id']  = $unit->id;
                }


                $propertyManagement->update($validatedData);

                if ($propertyManagement && $request->has('property_image')) {
                    $files = $request->file('property_image');
                    if (is_array($files)) {
                        array_map(function ($file) use ($propertyManagement) {
                            if ($file->isValid()) {
                                uploadImage($propertyManagement, $file, 'property/property-images', 'property_image', 'original', 'save', null);
                            }
                        }, $files);
                    }
                }

                if (isset($request->property_managementIds)) {
                    $documentIds = explode(',', $request->property_managementIds);
                    foreach ($documentIds as $documentId) {
                        deleteFile($documentId);
                    }
                }

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
        abort_if(Gate::denies('property_management_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $propertyManagement = PropertyManagement::where('uuid', $uuid)->first();
                if ($propertyManagement) {
                    $propertyManagement->delete();
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

    public function reports()
    {
        abort_if(Gate::denies('property_management_report'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $societies = Society::whereStatus(1)->latest()->get();
            // $propertyManagement = PropertyManagement::latest()->get();
            $lastSocietyUpdateDate = $societies->isNotEmpty() ? $societies->first()->updated_at->format('d-m-Y') : 'N/A';
            return view('backend.property-management.report', compact('societies', 'lastSocietyUpdateDate'));
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    public function filterBySociety(Request $request)
    {
        abort_if(Gate::denies('property_management_report'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $societyId = $request->get('society_id');
            $propertyManagement = PropertyManagement::where('society_id', $societyId)
                ->with(['maintenanceItem', 'propertyType', 'society'])
                ->get();

            $society = Society::find($societyId);
            $lastUpdateDate = $society ? $society->updated_at->format('d-m-Y') : 'N/A';
            $updateDateHtml = '<strong>' . trans('global.update_date') . ' :</strong> ' . $lastUpdateDate;
            return response()->json([
                'html' => view('backend.property-management._property_rows', compact('propertyManagement'))->render(),
                'lastUpdateDate' => $updateDateHtml,
            ]);
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    public function viewImage(Request $request)
    {
        abort_if(Gate::denies('property_management_view_images'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            try {
                $propertyManagement = PropertyManagement::find($request->id);
                $imageArrays = [];
                if ($propertyManagement) {
                    if (!empty($propertyManagement->property_management_images_image_urls) && is_array($propertyManagement->property_management_images_image_urls)) {
                        foreach ($propertyManagement->property_management_images_image_urls as $image) {
                            $imageArrays[] = asset($image);
                        }
                    }
                }
                $viewHTML = view('backend.property-management.show_image', compact('imageArrays'))->render();
                return response()->json(array('success' => true, 'htmlView' => $viewHTML));
            } catch (\Exception $e) {

                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }
}
