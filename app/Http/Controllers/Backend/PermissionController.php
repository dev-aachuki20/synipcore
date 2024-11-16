<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\PermissionDataTable;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;



class PermissionController extends Controller
{

    public function index(PermissionDataTable $dataTable)
    {
        abort_if(Gate::denies('permission_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $permissions = Permission::all();
            return $dataTable->render('backend.permission.index', compact('permissions'));
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    public function create()
    {
        abort_if(Gate::denies('permission_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return view('backend.permission.create');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('permission_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validatedData = $request->validate([
            'name' => 'required',
            'title' => 'required',
            'route_name' => 'required',
        ], [], [
            'name' => 'key',
            'title' => 'title',
            'route_name' => 'route name',
        ]);

        if ($request->ajax()) {

            DB::beginTransaction();
            try {
                Permission::create($validatedData);
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' =>  trans('messages.created_successfully'),
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }

    public function edit(Permission $permission)
    {
        abort_if(Gate::denies('permission_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return view('backend.permission.edit', compact('permission'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    public function update(Request $request, Permission $permission)
    {
        abort_if(Gate::denies('permission_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validatedData = $request->validate([
            'name' => 'required',
            'title' => 'required',
            'route_name' => 'required',
        ], [], [
            'name' => 'key',
            'title' => 'title',
            'route_name' => 'route name',
        ]);

        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $permission->update($validatedData);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'data' => $permission,
                    'message' =>  trans('messages.updated_successfully'),
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }

    public function destroy(Request $request, Permission $permission)
    {
        abort_if(Gate::denies('permission_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                // $permission = Permission::findOrFail($id);
                $permission->delete();
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
