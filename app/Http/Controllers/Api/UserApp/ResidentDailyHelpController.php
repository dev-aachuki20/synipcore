<?php

namespace App\Http\Controllers\Api\UserApp;

use App\Http\Controllers\Api\APIController;
use App\Models\ResidentDailyHelp;
use App\Rules\NoMultipleSpacesRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResidentDailyHelpController extends APIController
{
    public function index()
    {
        try {
            $user = Auth::user();

            $dailyHelps = $user->dailyhelps()->select('id', 'name', 'phone_number', 'help_type', 'created_at')
                ->latest()
                ->get()
                ->map(function ($dailyHelp) {
                    return [
                        'id' => $dailyHelp->id,
                        'name' => $dailyHelp->name,
                        'phone_number' => $dailyHelp->phone_number,
                        'help_type' => $dailyHelp->help_type,
                        'profile_image_url' => $dailyHelp->profile_image_url,
                        'gatepass_qr_image' => $dailyHelp->gatepass_qr_image,
                        'gatepass_type'     => 'daily_help',
                        'created_at'        => $dailyHelp->created_at->format(config('constant.date_format.date_time')),
                    ];
                });
            return $this->respondOk([
                'status'   => true,
                'data'   => $dailyHelps
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255', new NoMultipleSpacesRule],
            'phone_number'  => ['required', 'numeric', 'digits_between:10,15', 'unique:resident_daily_helps,phone_number,NULL,id,deleted_at,NULL'],
            'help_type'     => ['required'],
            'gatepass_code'     => ['required'],
            'gatepass_qr_image'     => ['required'],
            'profile_image'  => ['nullable', 'image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();

            $input = $request->only('name', 'phone_number', 'help_type', 'gatepass_code');

            $input['resident_id'] = $user->id;
            $input['created_by'] = $user->id;
            $input['updated_by'] = $user->id;

            $input['society_id'] = $user->society ? $user->society->id : null;
            $input['building_id'] = $user->building ? $user->building->id : null;
            $input['unit_id'] = $user->unit ? $user->unit->id : null;

            $dailyHelp = ResidentDailyHelp::create($input);

            // upload Gtepass QR image
            if ($dailyHelp && $request->has('profile_image')) {
                uploadImage($dailyHelp, $request->profile_image,  'daily-helps/profile-image', "daily_help_profile_image", 'original', 'save', null);
            }

            // upload Gtepass QR image
            if ($dailyHelp && $request->has('gatepass_qr_image')) {
                uploadImage($dailyHelp, $request->gatepass_qr_image,  'daily-helps/gatepass-qr', "daily_help_gatepass_qr", 'original', 'save', null);
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.frequest_entry.create_success')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function edit($id)
    {
        try {
            $dailyHelp = ResidentDailyHelp::whereId($id)->select('id', 'name', 'phone_number', 'help_type')->first();
            $dailyHelp['profile_image_url'] = $dailyHelp->profile_image_url;
            $dailyHelp['gatepass_qr_image'] = $dailyHelp->gatepass_qr_image;

            $dailyHelp = collect($dailyHelp)->except(['profile_image', 'daily_help_qr'])->all();
            return $this->respondOk([
                'status'   => true,
                'data'   => $dailyHelp
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function Update(Request $request, $id)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255', new NoMultipleSpacesRule],
            'phone_number'  => ['required', 'numeric', 'digits_between:10,15', 'unique:resident_daily_helps,phone_number,' . $id . ',id,deleted_at,NULL'],
            'help_type'     => ['required'],
            'help_type'     => ['required'],
            'profile_image'  => ['nullable', 'image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
        ]);

        DB::beginTransaction();
        try {
            $dailyHelp = ResidentDailyHelp::find($id);
            $user = Auth::user();
            if (!$dailyHelp) {
                return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
            }

            $input = $request->only('name', 'phone_number', 'help_type');
            $input['updated_by'] = $user->id;

            $dailyHelp->update($input);

            if ($request->has('profile_image')) {
                $uploadId = null;
                $actionType = 'save';
                if ($profileImageRecord = $dailyHelp->profileImage) {
                    $uploadId = $profileImageRecord->id;
                    $actionType = 'update';
                }
                uploadImage($dailyHelp, $request->profile_image, 'daily-helps/profile-image', "daily_help_profile_image", 'original', $actionType, $uploadId);
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.daily_help.update_success')
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
            $dailyHelp = ResidentDailyHelp::find($id);
            if ($dailyHelp) {
                if ($dailyHelp->profileImage) {
                    deleteFile($dailyHelp->profileImage->id);
                }
                if ($dailyHelp->dailyHelpQr) {
                    deleteFile($dailyHelp->dailyHelpQr->id);
                }
                $dailyHelp->delete();
            }

            DB::commit();
            return response()->json([
                'success'    => true,
                'message'    => trans('messages.daily_help.delete_success'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }
}
