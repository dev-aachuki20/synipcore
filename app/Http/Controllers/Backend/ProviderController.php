<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\ProviderDataTable;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Provider\StoreRequest;
use App\Http\Requests\Provider\UpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Society;
use Illuminate\Support\Arr;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ProviderDataTable $dataTable)
    {
        abort_if(Gate::denies('provider_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.provider.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('provider_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();
            $societies = Society::whereStatus(1)->latest()->get();
            return view('backend.provider.create', compact('societies', 'user'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('provider_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();
                $validatedData['society_id'] = $request->society_ids[0];

                $provider = User::create(Arr::except($validatedData, ['address']));
                $provider->roles()->sync([config('constant.roles.provider')]);

                $provider->societies()->attach($request->society_ids); //multiple

                if ($request->has('address')) {
                    $provider->profile()->create(['address' => $request->input('address')]);
                }
                if ($request->has('profile_image')) {
                    $uploadId = null;
                    $actionType = 'save';
                    if ($profileImageRecord = $provider->profileImage) {
                        $uploadId = $profileImageRecord->id;
                        $actionType = 'update';
                    }
                    uploadImage($provider, $request->profile_image, 'user/profile-images', "user_profile", 'original', $actionType, $uploadId);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $uuid)
    {
        abort_if(Gate::denies('provider_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $societies = Society::whereStatus(1)->latest()->get();
            $provider = User::where('uuid', $uuid)->first();
            if ($provider) {
                return view('backend.provider.edit', compact('provider', 'societies', 'user'));
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
        abort_if(Gate::denies('provider_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();

                $provider = User::where('uuid', $uuid)->first();
                if (!$provider) {
                    throw new \Exception('User not found');
                }

                $providerData = Arr::except($validatedData, ['address']);
                $providerData['society_id'] = $request->society_ids[0];
                $provider->update($providerData);

                if (!empty($validatedData['society_ids'])) {
                    $provider->societies()->sync($validatedData['society_ids']);
                } else {
                    $provider->societies()->detach();
                }


                if ($request->has('address')) {
                    $provider->profile()->create(['address' => $request->input('address')]);
                }

                // Handle profile image update
                if ($request->has('profile_image')) {
                    $uploadId = null;
                    $actionType = 'save';
                    if ($profileImageRecord = $provider->profileImage) {
                        $uploadId = $profileImageRecord->id;
                        $actionType = 'update';
                    }
                    uploadImage($provider, $request->profile_image, 'user/profile-images', "user_profile", 'original', $actionType, $uploadId);
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
        abort_if(Gate::denies('provider_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $provider = User::where('uuid', $uuid)->first();
                if ($provider) {
                    if ($provider->profile_image_url) {
                        $uploadImageId = $provider->profileImage->id;
                        deleteFile($uploadImageId);
                    }
                    // $provider->profile()->delete();
                    // $provider->features()->detach();
                    $provider->delete();
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

    public function isVerified(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:users,uuid',
            ]);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()->toArray(), 'message' => 'Error Occurred!',], 400);
            }

            DB::beginTransaction();
            try {
                $user = User::where('uuid', $request->id)->firstOrFail();
                $verified = isset($user) && ($user->is_verified == 0) ? 1 : 0;
                $user->update(['is_verified' => $verified]);

                DB::commit();
                return response()->json(['success'    => true, 'message'   => trans('messages.status_update_successfully'),]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
    }
}
