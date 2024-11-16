<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\RoleDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRequest;
use App\Http\Requests\Role\UpdateRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(RoleDataTable $dataTable)
    {
        abort_if(Gate::denies('role_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.role.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('role_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $permissions  = Permission::all();
            $roles = Role::all();
            return view('backend.role.create', compact('roles', 'permissions'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }
    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('role_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            DB::beginTransaction();
            $validatedData = $request->validated();
            try {
                $role = Role::create(['name' => $validatedData['role_name']]);
                if (isset($validatedData['permissions'])) {
                    $role->permissions()->sync($validatedData['permissions']);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'data' => $role,
                    'message' =>  trans('messages.created_successfully'),
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
    public function edit(Request $request, $id)
    {
        abort_if(Gate::denies('role_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $roles = Role::all();
            $roleuser = Role::with('permissions')->findOrFail($id);
            $permissions = Permission::all();
            return view('backend.role.edit', compact('roles', 'roleuser', 'permissions'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Role $role)
    {
        abort_if(Gate::denies('role_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {

            DB::beginTransaction();

            $validatedData = $request->validated();

            try {
                $role->update(['name' => $validatedData['role_name']]);

                if (isset($validatedData['permissions'])) {
                    // Sync permissions
                    $role->permissions()->sync($validatedData['permissions']);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'data' => $role,
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
    public function destroy(Request $request, Role $role)
    {
        abort_if(Gate::denies('role_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            DB::beginTransaction();

            try {
                $role->permissions()->detach();
                $role->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'data' => $role,
                    'message' =>  trans('messages.deleted_successfully'),
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }

        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }
}
