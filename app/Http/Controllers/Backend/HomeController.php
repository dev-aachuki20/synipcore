<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Society;
use App\Models\Unit;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function getSocietyBuildingUnits(Request $request)
    {
        try {
            if ($request->ajax()) {
                $id = $request['id'] ?? 0;
                if (isset($request['type']) && !empty($request['type']) && ($id > 0)) {
                    $html = '';
                    switch ($request['type']) {
                        case 'building':
                            $society = Society::where('uuid', $id)->first();
                            // Check if society exists
                            if (!$society) {
                                return response()->json(['success' => false, 'error' => 'Society not found'], 404);
                            }
                            $buildings = Building::whereStatus(1)->where('society_id', $society->id)->latest()->get();

                            $html = '<option value="">' . trans('global.select') . ' ' . trans('cruds.resident.fields.building') . '</option>';
                            $html .= $buildings->map(function ($building) {
                                return "<option value=" . $building->uuid . ">" . $building->title . "</option>";
                            })->implode('');
                            break;

                        case 'unit':
                            $building = Building::where('uuid', $id)->first();

                            if (!$building) {
                                return response()->json(['success' => false, 'error' => 'Building not found'], 404);
                            }
                            $units = Unit::whereStatus(1)->where('building_id', $building->id)->latest()->get();

                            $html = '<option value="">' . trans('global.select') . ' ' . trans('cruds.resident.fields.unit') . '</option>';
                            $html .= $units->map(function ($unit) {
                                return "<option value=" . $unit->uuid . ">" . $unit->title . "</option>";
                            })->implode('');
                            break;
                        default:
                            break;
                    }
                    return response()->json(['success' => true, 'message' => trans('messages.data_retrieved_successfully'), 'data' => $html]);
                }
            }
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }
}
