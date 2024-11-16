<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\UserDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\Language;
use App\Models\Role;
use App\Models\Society;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(UserDataTable $dataTable)
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.user.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $getAllSociety = Society::whereStatus(1)->latest()->get();
            $getAllRoles = Role::where('id', '!=', config('constant.roles.superadmin'))->get();
            $getAllLanguages = Language::whereStatus(1)->get();
            return view('backend.user.create', compact('getAllRoles', 'getAllSociety', 'getAllLanguages'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();
                $validatedData['society_id'] = $request->society_ids[0];
                $user = User::create($validatedData);

                if (isset($validatedData['roles'])) {
                    $user->roles()->sync($validatedData['roles']);
                }

                $user->societies()->attach($request->society_ids); //multiple

                do {
                    $securityPin = rand(100000, 999999);
                } while (User::where('security_pin', $securityPin)->exists());

                $qrcode = QrCode::size(300)->generate('Embed this content into the QR Code');
                $user->qr_code_path = $qrcode;
                $user->security_pin = $securityPin;
                $user->save();

                if ($request->has('profile_image')) {
                    $uploadId = null;
                    $actionType = 'save';
                    if ($profileImageRecord = $user->profileImage) {
                        $uploadId = $profileImageRecord->id;
                        $actionType = 'update';
                    }
                    uploadImage($user, $request->profile_image, 'user/profile-images', "user_profile", 'original', $actionType, $uploadId);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'data' => $user,
                    'message' => trans('messages.created_successfully')
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
    public function show(Request $request, string $id)
    {
        abort_if(Gate::denies('user_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            try {
                $user = User::where('uuid', $id)->first();
                $viewHTML = view('backend.user.show', compact('user'))->render();
                return response()->json(array('success' => true, 'htmlView' => $viewHTML));
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = User::where('uuid', $uuid)->first();
            $getAllSociety = Society::whereStatus(1)->latest()->get();
            $getAllRoles = Role::where('id', '!=', config('constant.roles.superadmin'))->get();
            $getAllLanguages = Language::whereStatus(1)->get();
            return view('backend.user.edit', compact('user', 'getAllRoles', 'getAllSociety', 'getAllLanguages'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $uuid)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $user = User::where('uuid', $uuid)->firstOrFail();
                $validatedData = $request->validated();
                $fieldsToUpdate = Arr::except($validatedData, ['email', 'mobile_number']);

                // Update the user record
                $fieldsToUpdate['society_id'] = $request->society_ids[0];
                $user->update($fieldsToUpdate);

                if (!empty($validatedData['roles'])) {
                    $user->roles()->sync($validatedData['roles']);
                } else {
                    $user->roles()->detach();
                }

                if (!empty($validatedData['society_ids'])) {
                    $user->societies()->sync($validatedData['society_ids']);
                } else {
                    $user->societies()->detach();
                }

                // Handle profile image update
                if ($request->has('profile_image')) {
                    $uploadId = null;
                    $actionType = 'save';
                    if ($profileImageRecord = $user->profileImage) {
                        $uploadId = $profileImageRecord->id;
                        $actionType = 'update';
                    }
                    uploadImage($user, $request->profile_image, 'user/profile-images', "user_profile", 'original', $actionType, $uploadId);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'data' => $user,
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
    public function destroy(Request $request, $id)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $user = User::where('uuid', $id)->first();
            DB::beginTransaction();
            try {
                if ($user) {
                    if ($user->profile_image_url) {
                        $uploadImageId = $user->profileImage->id;
                        deleteFile($uploadImageId);
                    }
                    $user->delete();
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

    public function changeStatus(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'id'     => [
                    'required',
                    'exists:users,uuid',
                ],
            ]);
            if (!$validator->passes()) {
                return response()->json(['success' => false, 'errors' => $validator->getMessageBag()->toArray(), 'message' => 'Error Occured!'], 400);
            } else {
                DB::beginTransaction();
                try {
                    $user = User::where('uuid', $request->id)->first();
                    if ($user->status == 0) {
                        $status = 1;
                    } else {
                        $status = 0;
                    }
                    $user->update(['status' => $status]);

                    DB::commit();
                    $response = [
                        'status'    => 'true',
                        'message'   => trans('messages.status_update_successfully'),
                    ];
                    return response()->json($response);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
                }
            }
        }
    }

    public function getAllRoles()
    {
        $roles = Role::pluck('name', 'id');
        return response()->json($roles);
    }
}
