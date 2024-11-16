<?php

namespace App\Http\Controllers\Api\UserApp;

use App\Http\Controllers\Api\APIController;

use App\Models\ResidentFamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResidentFamilyMemberController extends APIController
{
    public function index()
    {
        try {
            $user = Auth::user();
            $familymembers = $user->familymembers()->select('id', 'name', 'phone_number', 'relation', 'created_at')
                ->latest()
                ->get()
                ->map(function ($familymember) {
                    return [
                        'id'                => $familymember->id,
                        'name'              => $familymember->name,
                        'phone_number'      => $familymember->phone_number,
                        'relation'          => $familymember->relation,
                        'profile_image_url' => $familymember->profile_image_url,
                        'gatepass_qr_image' => $familymember->gatepass_qr_image,
                        'gatepass_type'     => 'family_member',
                        'created_at'        => $familymember->created_at->format(config('constant.date_format.date_time')),
                    ];
                });
            return $this->respondOk([
                'status'   => true,
                'data'   => $familymembers
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'phone_number'  => ['required', 'numeric', 'digits_between:10,15', 'unique:resident_family_members,phone_number,NULL,id,deleted_at,NULL'],
            'relation'          => ['required', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
            'gatepass_code' => ['required'],
            'gatepass_qr_image' => ['required'],
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();

            $input = $request->only('name', 'phone_number', 'relation', 'gatepass_code');

            $input['resident_id'] = $user->id;
            $familyMember = ResidentFamilyMember::create($input);

            // upload profile image
            if ($familyMember && $request->has('profile_image')) {
                uploadImage($familyMember, $request->profile_image,  'family-members/profile-image', "resident_family_member_profile_image", 'original', 'save', null);
            }

            // upload Gtepass QR image
            if ($familyMember && $request->has('gatepass_qr_image')) {
                uploadImage($familyMember, $request->gatepass_qr_image,  'family-members/gatepass-qr', "resident_family_member_qr", 'original', 'save', null);
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.family_member.create_success')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function edit($id)
    {
        try {
            $familyMember = ResidentFamilyMember::whereId($id)->select('id', 'name', 'phone_number', 'relation')->first();
            $familyMember['vehicle_image_url'] = $familyMember->profile_image_url;
            $familyMember['gatepass_qr_image'] = $familyMember->gatepass_qr_image;

            $familyMember = collect($familyMember)->except(['profile_image', 'family_member_qr'])->all();
            return $this->respondOk([
                'status'   => true,
                'data'   => $familyMember
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function Update(Request $request, $id)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'phone_number'  => ['required', 'numeric', 'digits_between:10,15', 'unique:resident_family_members,phone_number,' . $id . ',id,deleted_at,NULL'],
            'relation'      => ['required', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
        ]);

        DB::beginTransaction();
        try {
            $familyMember = ResidentFamilyMember::find($id);
            $user = Auth::user();
            if (!$familyMember) {
                return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
            }

            $input = $request->only('name', 'phone_number', 'relation');

            $familyMember->update($input);

            if ($request->has('profile_image')) {
                $uploadId = null;
                $actionType = 'save';
                if ($profileImageRecord = $familyMember->profileImage) {
                    $uploadId = $profileImageRecord->id;
                    $actionType = 'update';
                }
                uploadImage($familyMember, $request->profile_image, 'family-members/profile-image', "resident_family_member_profile_image", 'original', $actionType, $uploadId);
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.family_member.update_success')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $familyMember = ResidentFamilyMember::find($id);
            if ($familyMember) {
                if ($familyMember->profileImage) {
                    deleteFile($familyMember->profileImage->id);
                }
                if ($familyMember->familyMemberQr) {
                    deleteFile($familyMember->familyMemberQr->id);
                }
                $familyMember->delete();
            }

            DB::commit();
            return response()->json([
                'success'    => true,
                'message'    => trans('messages.family_member.delete_success'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }
}
