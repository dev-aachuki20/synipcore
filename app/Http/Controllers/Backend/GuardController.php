<?php

namespace App\Http\Controllers\Backend;

use Symfony\Component\HttpFoundation\Response;
use App\DataTables\GuardDataTable;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Guard\StoreRequest;
use App\Http\Requests\Guard\UpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Society;
use App\Models\Building;
use App\Models\Unit;

class GuardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GuardDataTable $dataTable)
    {
        abort_if(Gate::denies('guard_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.guard.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('guard_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();
            $models = [
                'societies' => $user->is_sub_admin ? $user->societies : Society::whereStatus(1)->latest()->get(),
                // 'type' => 'create',
            ];
            return view('backend.guard.create', compact('models'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('guard_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $input = $request->validated();
                $store = User::create($input);
                $store->roles()->sync([config('constant.roles.guard')]);

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
    public function show(Request $request, string $id)
    {
        abort_if(Gate::denies('guard_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
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
        abort_if(Gate::denies('guard_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            $guard = User::where('uuid', $uuid)->first();
            if (!$guard) {
                return response()->json(['success' => false, 'error_type' => 'not_found', 'error' => trans('messages.not_found')], 404);
            }

            $user = auth()->user();
            $models = [
                'societies' => $user->is_sub_admin ? $user->societies : Society::whereStatus(1)->latest()->get(),
                'buildings' => Building::where('society_id', $guard->society_id)->whereStatus(1)->latest()->get(),
                'units' => Unit::where('society_id', $guard->society_id)->whereStatus(1)->latest()->get(),
                // 'type' => 'edit'
            ];

            return view('backend.guard.edit', compact('guard', 'models'));
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
        abort_if(Gate::denies('guard_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $input = $request->validated();
                $guard = User::where('uuid', $uuid)->first();
                if (!$guard) {
                    throw new \Exception('User not found');
                }
                $guard->update($input);

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
        abort_if(Gate::denies('guard_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $guard = User::where('uuid', $uuid)->first();
                if ($guard) {
                    $guard->delete();
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
}
