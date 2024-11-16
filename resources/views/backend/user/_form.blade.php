<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.user.fields.name') }}<span class="required">*</span></label>
            <input type="text" class="form-control" name="name" value="{{ isset($user) ? $user->name : '' }}" placeholder="{{ trans('cruds.user.fields.name') }}" />
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.user.fields.email') }}<span class="required">*</span></label>
            <input type="text" class="form-control" name="email" {{ isset($user) ? 'disabled' : '' }} {{ isset($user) ? 'readonly' : '' }}
                value="{{ isset($user) ? $user->email : '' }}" placeholder="{{ trans('cruds.user.fields.email') }}" />
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.user.fields.password') }}<span
                    class="required">*</span></label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password" placeholder="{{ trans('cruds.user.fields.password') }}" {{ isset($user) ? 'disabled' : '' }} {{ isset($user) ? 'readonly' : '' }} />
                <div class="input-group-text toggle-password show-password">
                    <span class="password-eye"></span>
                </div>
            </div>
            @if (isset($user) && isset($user->mobile_number))
            <div class="text-end align-right-btn">
                <a href="#" class="resetPasswordOtp" id="resetPasswordOtp">{{trans('global.reset_password')}}</a>
            </div>
            <div>
                <span class="otp-wait-message"></span>
            </div>
            <div id="recaptcha-container"></div>

            @endif
        </div>
    </div>
    <div class="col-lg-6 d-none enter_otp">
        <div class="form-group">
            <label class="form-label">{{trans('global.enter_otp')}}<span class="required">*</span></label>
            <div class="input-group">
                <input type="number" class="form-control" name="otp" id="otp-input" placeholder="{{trans('global.enter_otp')}}" />
                <button class="btn btn-primary verify-otp">{{trans('global.verify')}}</button>
            </div>
            <div class="text-end align-right-btn">
                <span class="otp-error-message text-danger d-none"></span>
                <a href="#" class="resend-otp d-none">{{trans('global.resend_otp')}}</a>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.user.fields.mobile') }}<span class="required">*</span></label>
            <div class="form-group d-flex align-items-center number_prefix">
                <input type="number" class="form-control" name="mobile_number" {{ isset($user) ? 'disabled' : '' }}
                    value="{{ isset($user) ? $user->mobile_number : '' }}" placeholder="{{ trans('cruds.user.fields.mobile') }}" />
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.user.fields.roles') }}</label>
            <select name="roles[]" class="select2 form-control select2-multiple" data-toggle="select2"
                multiple="multiple" data-placeholder="{{trans('global.choose')}}">
                @foreach ($getAllRoles as $role)
                <option value="{{ $role->id }}" {{ isset($user) && in_array($role->id, $user->roles->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.user.fields.language') }}<span class="required">*</span></label>
            <select name="language_id" class="form-control">
                <option value="">{{ trans('global.select') }}</option>
                @foreach ($getAllLanguages as $language)
                <option value="{{ $language->id }}"
                    {{ isset($user) && $user->language_id == $language->id ? 'selected' : '' }}>
                    {{ $language->name }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

<div class="col-lg-6">
    <div class="form-group">
        <label class="form-label">@lang('cruds.menus.societies')<span class="required">*</span></label>
        <select name="society_ids[]" class="select2 form-control select2-multiple" data-toggle="select2"
            multiple="multiple" data-placeholder="{{trans('global.choose')}}">
            @foreach ($getAllSociety as $society)
            <option value="{{ $society->id }}" {{ isset($user) && $user->societies && $user->societies->contains('id', $society->id) ? 'selected' : '' }}>
                {{ $society->name }}
            </option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-lg-6">
    <div class="form-group">
        <label class="form-label" for="phone">{{ trans('global.profile') }} {{ trans('global.image') }}</label>
        <div class="UploadBtn">
            <input type="file" id="image-input" name="profile_image" class="form-control fileInputBoth" accept="image/*">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="19" viewBox="0 0 24 19" fill="none" class="me-1">
                <path d="M19.9963 6.24269C18.8871 1.82389 14.4058 -0.859034 9.98705 0.250189C6.53386 1.11705 4.02646 4.10158 3.76811 7.65249C1.31621 8.05684 -0.343691 10.3723 0.0606558 12.8242C0.420101 15.0039 2.30886 16.6003 4.51803 16.5915H8.26762V15.0917H4.51803C2.86137 15.0917 1.51836 13.7487 1.51836 12.092C1.51836 10.4353 2.86137 9.09234 4.51803 9.09234C4.93222 9.09234 5.26795 8.75661 5.26795 8.34242C5.2642 4.6149 8.28295 1.59011 12.0105 1.5864C15.2372 1.58317 18.0148 3.86433 18.639 7.03006C18.7006 7.34615 18.9571 7.58763 19.2764 7.63C21.3266 7.92195 22.7519 9.8206 22.46 11.8707C22.1978 13.7117 20.626 15.0824 18.7665 15.0917H15.7668V16.5915H18.7665C21.6657 16.5828 24.0088 14.2254 24 11.3262C23.9927 8.91287 22.3408 6.8154 19.9963 6.24269Z" fill="#6c757d" />
                <path d="M11.485 9.30988L8.48535 12.3096L9.54274 13.3669L11.2675 11.6496V18.8413H12.7674V11.6496L14.4847 13.3669L15.5421 12.3096L12.5424 9.30988C12.2499 9.0191 11.7775 9.0191 11.485 9.30988Z" fill="#6c757d" />
            </svg> {{trans('global.upload_file')}}
        </div>
        <div class="img-prevarea mt-3">
            <img src="{{ isset($user) && $user->profile_image_url ? $user->profile_image_url : asset(config('constant.default.user_icon')) }}"
                width="100px" height="100px">
        </div>
    </div>
</div>
<div class="col-lg-12">
    <div class="form-group">
        <label class="form-label">{{ trans('cruds.user.fields.description') }}</label>
        <textarea name="description" id="description" cols="30" rows="10" class="form-control" placeholder="{{ trans('cruds.user.fields.description') }}">{{ isset($user) ? $user->description : '' }}</textarea>
    </div>
</div>
<div class="col-lg-3">
    <div class="form-group">
        <input id="mobile_verified" type="checkbox" name="verified" {{ isset($user) && !empty($user->mobile_verified) && ($user->mobile_verified == 1) ? 'checked' : '' }} />
        <label for="mobile_verified" class="form-label">{{ trans('cruds.user.fields.mobile_verified') }}</label>
    </div>
</div>

<div class="col-lg-3">
    <div class="form-group">
        <input id="is_enabled" type="checkbox" name="status" {{ isset($user) && !empty($user->status) && ($user->status == 1) ? 'checked' : (!isset($user) ? 'checked' : '') }} />
        <label for="status" class="form-label">{{ trans('cruds.user.fields.is_enabled') }}</label>
    </div>
</div>
<div class="col-lg-12">
    <div class="bottombtn-group">
        <button type="submit"
            class="btn btn-success submitBtn">{{ !isset($user) ? trans('global.save') : trans('global.update') }}</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
    </div>
</div>
</div>