<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\APIController;
use App\Http\Requests\Auth\RegisterRequest;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class AuthController extends APIController
{
    protected $token_type = 'Bearer';

    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $userData = [
                'name'          => trim($request->name),
                'login_type'    => 'normal',
                'email'         => $request->email,
                'password'      => Hash::make($request->password),
                'mobile_number' => $request->mobile_number,
                'language_id'   => $request->language_id ?? 1,
                'resident_type' => 3, // $request->resident_type
                'location_id'   => $request->location_id,
                'district_id'   => $request->district_id,
                'society_id'    => $request->society_id,
                'building_id'   => $request->building_id,
                'unit_id'       => $request->unit_id,
                'device_token'  => $request->device_token,
                'last_login_at' => now(),
                'current_session_id' => $request->header('X-Device-Id'),
            ];
            $user = User::create($userData);

            if (!$user) {
                return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
            }

            // upload profile image
            if ($user && $request->has('profile_image') && !empty($request->profile_image)) {
                uploadImage($user, $request->profile_image,  'user/profile-images', "user_profile", 'original', 'save', null);
            }

            $user->roles()->sync([config('constant.roles.resident')]);

            $accessToken = $user->createToken(config('auth.api_token_name'))->plainTextToken;

            do {
                $securityPin = rand(100000, 999999);
            } while (User::where('security_pin', $securityPin)->exists());

            $qrcode = QrCode::size(300)->generate('Embed this content into the QR Code');
            $user->qr_code_path = $qrcode;
            $user->security_pin = $securityPin;
            $user->save();

            DB::commit();
            return $this->respond([
                'status'            => true,
                'message'           => trans('messages.login_success'),
                'token_type'        => $this->token_type,
                'access_token'      => $accessToken,
                'data'              => [
                    'id'                => $user->id,
                    'uuid'              => $user->uuid,
                    'name'              => $user->name,
                    'mobile_number'     => $user->mobile_number ?? '',
                    'email'             => $user->email ?? '',
                    'social_user_id'    => $user->social_user_id ?? '',
                    'login_type'        => $user->login_type,
                    'location'          => $user->city ? $user->city->title : '',
                    'district'          => $user->district ? $user->district->title : '',
                    'society'           => $user->society ? $user->society->name : '',
                    'building'          => $user->building ? $user->building->title : '',
                    'unit'              => $user->unit ? $user->unit->title : '',
                    'profile_image'     => $user->profile_image_url,
                    'status'            => $user->status,
                    'language_id'       => $user->language_id,
                    'language_code'     => $user->userLanguage->code ?? 'en',
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_type'   => ['required','in:google,normal,facebook'],
            'language_id' => ['required', 'exists:languages,id']
        ]);

        if ($request->login_type == 'normal') {
            $user = $this->normalLogin($request);
        } else {
            $user = $this->socialLogin($request);
        }
        if (!$user) {
            return $this->setStatusCode(401)->respondWithError(trans('auth.failed'));
        }
        if ($user['user_status'] === false || $user->status != 1) {
            return $this->setStatusCode(401)->respondWithError(trans('messages.user_account_deactivate'));
        }

        DB::beginTransaction();
        try {
            // update user login
            $user->update(['language_id' => $request->language_id]);

            if ($user && empty($user->society_id)) {
                return $this->respond([
                    'status'            => true,
                    'society_verify'    => false,
                    'user_id'           => $user->id,
                    'message'           => trans('messages.society.not_verified'),
                ]);
            }

            $userData = [
                'device_token'  => $request->device_token,
                'last_login_at' => now(),
                'current_session_id' => $request->header('X-Device-Id'),
            ];
            $user->update($userData);

            $accessToken = $user->createToken(config('auth.api_token_name'))->plainTextToken;

            DB::commit();
            return $this->respond([
                'status'            => true,
                'message'           => trans('messages.login_success'),
                'token_type'        => $this->token_type,
                'access_token'      => $accessToken,
                'data'              => [
                    'id'                => $user->id,
                    'uuid'              => $user->uuid,
                    'name'              => $user->name,
                    'mobile_number'     => $user->mobile_number ?? '',
                    'email'             => $user->email ?? '',
                    'social_user_id'    => $user->social_user_id ?? '',
                    'login_type'        => $user->login_type,
                    'location'       => $user->city ? $user->city->title : '',
                    'district'       => $user->district ? $user->district->title : '',
                    'society'        => $user->society ? $user->society->name : '',
                    'building'       => $user->building ? $user->building->title : '',
                    'unit'           => $user->unit ? $user->unit->title : '',
                    'profile_image'     => $user->profile_image_url,
                    'language_id'       => $user->language_id,
                    'language_code'     => $user->userLanguage->code ?? 'en',
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    // normal login
    private function normalLogin($request)
    {
        $request->validate([
            'user_login' => ['required'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user_login = $request->user_login;

        $loginType = filter_var($user_login, FILTER_VALIDATE_EMAIL) ? 'email' : (is_numeric($user_login) ? 'mobile_number' : 'username');

        if (Auth::attempt([$loginType => $user_login, 'password' => $request->password])) {
            $user = Auth::user();
            return $user;
        } else {
            return false;
        }
    }

    // social login
    private function socialLogin($request)
    {
        $userData = [];

        if ($request->has('email') && !empty($request->email)) {
            $user = User::where('email', $request->email)->first();
        } else {
            $user = User::where('social_user_id', $request->social_user_id)->first();
        }
        if (!$user) {
            $rules = [];
            $rules['name'] = ['required', 'string', 'max:255'];
            if ($request->has('email') && !empty($request->email)) {
                $rules['email'] = ['nullable', 'required_if:login_type,google', 'email', 'regex:/^(?!.*[\/]).+@(?!.*[\/]).+\.(?!.*[\/]).+$/i', 'unique:users,email,NULL,id,deleted_at,NULL'];
            }
            $data = $request->validate($rules);

            $userData['login_type'] = $request->login_type;
            $userData['name'] = $request->name;
            $userData['social_user_id'] = $request->social_user_id;

            if (!empty($request->email)) {
                $userData['email'] = $request->email;
                $userData['email_verified_at'] = now();
            }

            $userData['language_id']    = $request->language_id ?? 1;
            $userData['status']         = 0;
            $userData['resident_type']  = 3;

            $user = User::create($userData);

            $user->roles()->sync([config('constant.roles.resident')]);
        } else {
            $rules = [];
            if ($request->has('email') && !empty($request->email)) {
                $rules['email'] = ['nullable', 'required_if:login_type,google', 'email', 'regex:/^(?!.*[\/]).+@(?!.*[\/]).+\.(?!.*[\/]).+$/i'];
            }
            $request->validate($rules);

            $user->tokens()->delete();
            if ($user->status != 1) {
                return ['user_status' => false];
            }
        }

        if (!$user) {
            return false;
        }
        Auth::login($user);

        return $user;
    }

    // Society details
    public function getSocietyDetails(Request $request)
    {
        $request->validate([
            'user_id'       => ['required', 'exists:users,id'],
            'location_id'   => ['required', 'exists:locations,id'],
            'district_id'   => ['required', 'exists:locations,id'],
            'society_id'    => ['required', 'exists:societies,id'],
            'building_id'   => ['required', 'exists:buildings,id'],
            'unit_id'       => ['required', 'exists:units,id'],
        ]);

        DB::beginTransaction();
        try {
            $user = User::find($request->user_id);
            $userData = [
                'location_id'    => $request->location_id,
                'district_id'    => $request->district_id,
                'society_id'    => $request->society_id,
                'building_id'   => $request->building_id,
                'unit_id'       => $request->unit_id,
                'status'        => 1,
                'device_token'  => $request->device_token,
                'current_session_id' => $request->header('X-Device-Id'),
            ];
            $user->update($userData);

            do {
                $securityPin = rand(100000, 999999);
            } while (User::where('security_pin', $securityPin)->exists());

            $qrcode = QrCode::size(300)->generate('Embed this content into the QR Code');
            $user->qr_code_path = $qrcode;
            $user->security_pin = $securityPin;
            $user->save();

            $user->refresh();

            if (!$user) {
                return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
            }

            Auth::login($user);

            $accessToken = $user->createToken(config('auth.api_token_name'))->plainTextToken;

            DB::commit();
            return $this->respond([
                'status'            => true,
                'message'           => trans('messages.login_success'),
                'token_type'        => $this->token_type,
                'access_token'      => $accessToken,
                'data'              => [
                    'id'                => $user->id,
                    'uuid'              => $user->uuid,
                    'name'              => $user->name,
                    'mobile_number'     => $user->mobile_number ?? '',
                    'email'             => $user->email ?? '',
                    'social_user_id'    => $user->social_user_id ?? '',
                    'login_type'        => $user->login_type,
                    'location'       => $user->city ? $user->city->title : '',
                    'district'       => $user->district ? $user->district->title : '',
                    'society'        => $user->society ? $user->society->name : '',
                    'building'       => $user->building ? $user->building->title : '',
                    'unit'           => $user->unit ? $user->unit->title : '',
                    'profile_image'     => $user->profile_image_url,
                    'security_pin'      => $user->security_pin ?? '',
                    'qr_code_path'      => $user->qr_code_path ?? '',
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    // forget password
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => ['required', 'email', 'exists:users,email,deleted_at,NULL']]);

        DB::beginTransaction();
        try {
            $user = User::where('email', $request->email)->firstOrFail();
            $token = rand(100000, 999999);

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            DB::table('password_reset_tokens')
                ->updateOrInsert(
                    ['email' => $request->email, 'token' => $token, 'created_at' => Carbon::now()],
                    ['email' => $request->email]
                );

            $subject = "Reset Password OTP";
            $expiretime = config('auth.passwords.users.otp_expire') . ' Minutes';
            $user->sendPasswordResetOtpNotification($user, $token, $subject, $expiretime);
            DB::commit();

            return $this->respondOk([
                'status' => true,
                'message' => trans('auth.messages.forgot_password.otp_sent'),
            ])->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    // Verify forget password OTP
    public function verifyOtp(Request $request)
    {
        try {
            $validation =  $request->validate([
                'email' => 'required|email|exists:password_reset_tokens,email',
                'otp'   => 'required|numeric|min:8'
            ]);


            $passwordReset = DB::table('password_reset_tokens')
                ->where('token', $request->otp)
                ->where('email', $request->email)
                ->latest()
                ->first();

            if (!$passwordReset) {
                return $this->throwValidation(trans('auth.messages.forgot_password.validation.invalid_otp'));
            }

            if (Carbon::parse($passwordReset->created_at)->addMinutes(config('auth.passwords.users.otp_expire'))->isPast()) {
                return $this->throwValidation(trans('auth.messages.forgot_password.validation.expire_otp'));
            }

            return $this->respondOk([
                'success' => true,
                'token' => encrypt($request->otp),
                'message' => trans('auth.messages.forgot_password.validation.verified_otp'),
            ]);
        } catch (\Exception $e) {
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    // Reset Password
    public function resetPassword(Request $request)
    {
        $validation = $request->validate([
            'token'     => 'required',
            'email'     => 'required|email|exists:users,email,deleted_at,NULL',
            'password'  => 'required|string|min:8',
            'confirmed_password' => 'required|string|same:password',
        ]);
        DB::beginTransaction();
        try {
            $token = decrypt($request->token);
            $passwordReset = DB::table('password_reset_tokens')->where('token', $token)
                ->where('email', $request->email)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$passwordReset) {
                return $this->throwValidation(trans('auth.messages.forgot_password.validation.invalid_token_email'));
            }

            /* $expireTime = config('auth.passwords.users.otp_expire');
            if (Carbon::parse($passwordReset->created_at)->addMinutes($expireTime)->isPast()) {
                DB::table('password_reset_tokens')->where('email',$passwordReset->email)->delete();
                return $this->throwValidation(trans('auth.messages.forgot_password.validation.expire_otp'));            
            } */

            $user = User::where('email', $passwordReset->email)->first();
            if (!$user) {
                return $this->throwValidation(trans('auth.messages.forgot_password.validation.email_not_found'));
            }

            $user->password = bcrypt($request->password);
            $user->save();
            DB::table('password_reset_tokens')->where('email', $passwordReset->email)->delete();

            DB::commit();

            return $this->respondOk([
                'success' => true,
                'message' => trans('auth.messages.forgot_password.success_update'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }


    // guard login API
    public function guardLogin(Request $request)
    {
        $request->validate([
            'language_id' => ['required', 'exists:languages,id'],
            'society_id' => ['required', 'exists:societies,id']
        ]);

        $pin = $request->security_pin;
        $user = User::where('security_pin', $pin)->where('society_id', $request->society_id)->first();

        if (!$user) {
            return $this->setStatusCode(401)->respondWithError(trans('auth.failed'));
        }
        if ($user['user_status'] === false || $user->status != 1) {
            return $this->setStatusCode(401)->respondWithError(trans('messages.user_account_deactivate'));
        }

        DB::beginTransaction();
        try {
            // update user login
            $user->update(['language_id' => $request->language_id]);

            if ($user && empty($user->society_id)) {
                return $this->respond([
                    'status'            => true,
                    'society_verify'    => false,
                    'user_id'           => $user->id,
                    'message'           => trans('messages.society.not_verified'),
                ]);
            }

            $userData = [
                'device_token'  => $request->device_token,
                'last_login_at' => now(),
                'current_session_id' => $request->header('X-Device-Id'),
                'guard_duty_status' => 1,
            ];
            $user->update($userData);

            $accessToken = $user->createToken(config('auth.api_token_name'))->plainTextToken;

            DB::commit();
            return $this->respond([
                'status'            => true,
                'message'           => trans('messages.login_success'),
                'token_type'        => $this->token_type,
                'access_token'      => $accessToken,
                'data'              => [
                    'id'                => $user->id,
                    'uuid'              => $user->uuid,
                    'name'              => $user->name,
                    'mobile_number'     => $user->mobile_number ?? '',
                    'email'             => $user->email ?? '',
                    'social_user_id'    => $user->social_user_id ?? '',
                    'society_id'        => $user->society_id,
                    'login_type'        => $user->login_type,
                    'location'          => $user->city ? $user->city->title : '',
                    'district'          => $user->district ? $user->district->title : '',
                    'society'           => $user->society ? $user->society->name : '',
                    'building'          => $user->building ? $user->building->title : '',
                    'unit'              => $user->unit ? $user->unit->title : '',
                    'profile_image'     => $user->profile_image_url,
                    'guard_duty_status' => 1,
                    'language_id'       => $user->language_id,
                    'language_code'     => $user->userLanguage->code ?? 'en',
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }
}
