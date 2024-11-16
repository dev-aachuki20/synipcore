<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\MaintenancePlanDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\MaintenancePlan\StoreRequest;
use App\Http\Requests\MaintenancePlan\UpdateRequest;
use App\Models\MaintenanceItem;
use App\Models\MaintenancePlan;
use App\Models\MaintenancePlanItem;
use App\Models\Society;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;



class MaintenancePlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MaintenancePlanDataTable $dataTable)
    {
        abort_if(Gate::denies('maintenance_plan_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.maintenance-plan.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('maintenance_plan_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();
            $societies = Society::whereStatus(1)->latest()->get();
            // $categories = Category::whereStatus(1)->latest()->get();
            $maintenanceItem = MaintenanceItem::whereStatus(1)->latest()->get();
            return view('backend.maintenance-plan.create', compact('societies' /* , 'categories' */, 'maintenanceItem', 'user'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('maintenance_plan_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();

                // Store the maintenance plan
                $maintenancePlan = MaintenancePlan::create([
                    'society_id'    => $validatedData['society_id'],
                    'year_of'       => $validatedData['year_of'],
                    // 'category_id'   => $validatedData['category_id'],
                    'total_budget'  => $validatedData['total_budget'],
                ]);

                // Loop through each item and store its details
                foreach ($validatedData['item'] as $item) {
                    // Store each maintenance plan item
                    MaintenancePlanItem::create([
                        'maintenance_plan_id' => $maintenancePlan->id,
                        'maintenance_item_id' => $item['maintenance_item_id'][0],
                        'comments'            => $item['comments'][0] ?? null,
                        'month'               => json_encode($item['month'] ?? []),
                        'budget'              => $item['budget'][0] ?? null,
                    ]);
                }


                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => $maintenancePlan,
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

    public function show(Request $request, string $uuid)
    {
        abort_if(Gate::denies('maintenance_plan_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $planItems = MaintenancePlan::with('items.maintenanceItem.category')->where('uuid', $uuid)->first();
            $groupedItems = $planItems->items->groupBy(function ($item) {
                return $item->maintenanceItem->category ? $item->maintenanceItem->category->title : '';
            });
            return view('backend.maintenance-plan.show', compact('planItems', 'groupedItems'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $uuid)
    {
        abort_if(Gate::denies('maintenance_plan_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();
            $societies = Society::whereStatus(1)->latest()->get();
            // $categories = Category::whereStatus(1)->latest()->get();
            $maintenanceItem = MaintenanceItem::whereStatus(1)->latest()->get();
            $maintenancePlan = MaintenancePlan::where('uuid', $uuid)->first();
            $maintenancePlanItems = $maintenancePlan->items;
            return view('backend.maintenance-plan.edit', compact('societies' /* , 'categories' */, 'maintenanceItem', 'maintenancePlan', 'user', 'maintenancePlanItems'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $uuid)
    {
        abort_if(Gate::denies('maintenance_plan_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();

                $maintenancePlan = MaintenancePlan::where('uuid', $uuid)->first();

                $maintenancePlan->update([
                    'society_id'    => $validatedData['society_id'],
                    'year_of'       => $validatedData['year_of'],
                    // 'category_id'   => $validatedData['category_id'],
                    'total_budget'  => $validatedData['total_budget'],
                ]);

                $idArray = array_map(function ($item) {
                    return isset($item['id']) ? $item['id'] : null;
                }, $request['item']);

                if (count($idArray) > 0) {
                    MaintenancePlanItem::where('maintenance_plan_id', $maintenancePlan->id)->whereNotIn('id', $idArray)->delete();
                }

                foreach ($request['item'] as $item) {
                    if (isset($item['id']) && !empty($item['id'])) {
                        $existingItem = MaintenancePlanItem::find($item['id']);
                        if ($existingItem) {
                            $existingItem->update([
                                'maintenance_item_id'   => $item['maintenance_item_id'][0] ?? null,
                                'comments'              => $item['comments'][0] ?? null,
                                'month'                 => json_encode($item['month'] ?? []),
                                'budget'                => $item['budget'][0] ?? null,
                            ]);
                        }
                    } else {
                        MaintenancePlanItem::create([
                            'maintenance_plan_id' => $maintenancePlan->id,
                            'maintenance_item_id' => $item['maintenance_item_id'][0],
                            'comments'            => $item['comments'][0] ?? null,
                            'month'               => json_encode($item['month'] ?? []),
                            'budget'              => $item['budget'][0] ?? null,
                        ]);
                    }
                }

                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => $maintenancePlan,
                    'message' => trans('messages.updated_successfully'),
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
        abort_if(Gate::denies('maintenance_plan_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $maintenancePlan = MaintenancePlan::where('uuid', $uuid)->first();
            DB::beginTransaction();
            try {
                if ($maintenancePlan) {
                    $maintenancePlan->delete();
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
