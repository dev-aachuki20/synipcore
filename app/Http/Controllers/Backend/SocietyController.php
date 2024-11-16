<?php

namespace App\Http\Controllers\Backend;

use Symfony\Component\HttpFoundation\Response;
use App\DataTables\SocietyDataTable;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Society\StoreRequest;
use App\Http\Requests\Society\UpdateRequest;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Society;

class SocietyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SocietyDataTable $dataTable)
    {
        abort_if(Gate::denies('society_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.society.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('society_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $cities =   Location::where('scope_id', 3)->pluck('title', 'id')->toArray();
            // $districts = Location::where('scope_id', 4)->pluck('title', 'id')->toArray();

            return view('backend.society.create', compact('cities' /* , 'districts' */));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('society_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $input = $request->validated();
                $store = Society::create($input);

                if ($store && $request->has('key') && $request->has('value')) {
                    $meta = metakeyField($request->input('key'), $request->input('value'), $store, 'save');
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
        abort_if(Gate::denies('society_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
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
    public function edit(Request $request, $uuid)
    {
        abort_if(Gate::denies('society_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            $society = Society::where('uuid', $uuid)->first();
            if ($society) {
                $cities =   Location::where('scope_id', 3)->pluck('title', 'id')->toArray();
                // $districts = Location::where('scope_id', 4)->pluck('title', 'id')->toArray();
                return view('backend.society.edit', compact('society', 'cities'));
            }
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
        abort_if(Gate::denies('society_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $input = $request->validated();
                $society = Society::where('uuid', $input['id'])->first();
                $society->update($input);

                if ($society && $request->has('key') && $request->has('value')) {
                    $meta = metakeyField($request->input('key'), $request->input('value'), $society, 'update');
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
        abort_if(Gate::denies('society_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $society = Society::where('uuid', $uuid)->first();
                if ($society) {
                    $society->delete();
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

    public function getAllSocieties()
    {
        $societies = Society::pluck('name', 'id');
        return response()->json($societies);
    }

    public function getDistrictsByCity(Request $request)
    {
        try {
            $districts = Location::where('scope_id', 4)
                ->where('parent_id', $request->input('city_id'))
                ->pluck('title', 'id')
                ->toArray();
            // dd($districts);

            return response()->json(['districts' => $districts]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Failed to load districts'], 500);
        }
    }
}
