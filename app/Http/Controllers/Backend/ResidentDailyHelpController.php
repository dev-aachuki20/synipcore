<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\ResidentDailyHelpDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResidentDailyHelp\StoreRequest;
use App\Http\Requests\ResidentDailyHelp\UpdateRequest;
use App\Models\Building;
use App\Models\ResidentDailyHelp;
use App\Models\Society;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;



class ResidentDailyHelpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ResidentDailyHelpDataTable $dataTable)
    {
        abort_if(Gate::denies('resident_daily_help_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.resident-daily-help.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('resident_daily_help_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();
            $societies = Society::whereStatus(1)->latest()->pluck('name', 'uuid', 'id')->toArray();
            $buildings  = Building::where('society_id', $user->society_id)->whereStatus(1)->latest()->pluck('title', 'uuid')->toArray();
            return view('backend.resident-daily-help.create', compact('societies', 'buildings', 'user'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('resident_daily_help_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();
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


                ResidentDailyHelp::create($validatedData);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $uuid)
    {
        abort_if(Gate::denies('resident_daily_help_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            $user = auth()->user();

            $residentDailyHelp = ResidentDailyHelp::where('uuid', $uuid)->first();
            if (!$residentDailyHelp) {
                return abort(500);
            }
            $societies  = Society::whereStatus(1)->latest()->pluck('name', 'uuid', 'id')->toArray();
            $buildings  = Building::where('society_id', $user->society_id)->whereStatus(1)->latest()->pluck('title', 'uuid')->toArray();
            $units      = Unit::where('society_id', $user->society_id)->whereStatus(1)->latest()->pluck('title', 'uuid')->toArray();
            return view('backend.resident-daily-help.edit', compact('residentDailyHelp', 'societies', 'units', 'buildings', 'user'));
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
        abort_if(Gate::denies('resident_daily_help_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $residentDailyHelp = ResidentDailyHelp::where('uuid', $uuid)->first();
                if (!$residentDailyHelp) {
                    return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 500);
                }
                $validatedData = $request->validated();

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

                $residentDailyHelp->update($validatedData);
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
        abort_if(Gate::denies('resident_daily_help_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $dailyHelp = ResidentDailyHelp::where('uuid', $uuid)->first();
            DB::beginTransaction();
            try {
                if ($dailyHelp) {
                    $dailyHelp->delete();
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
