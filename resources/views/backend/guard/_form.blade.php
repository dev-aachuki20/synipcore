<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.guard.fields.name') }}<span class="required">*</span></label>
            <input type="text" id="name" class="form-control" name="name" value="{{ isset($guard) && !empty($guard->name) ? $guard->name : '' }}" placeholder="@lang('cruds.guard.fields.name')">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.guard.fields.email') }}</label>
            <input type="text" class="form-control" name="email" autocomplete="off" value="{{ isset($guard) && !empty($guard->email) ? $guard->email : '' }}" placeholder="@lang('cruds.guard.fields.email')" />
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.guard.fields.mobile') }}<span class="required">*</span></label>
            <input type="number" class="form-control" name="mobile_number" value="{{ isset($guard) && !empty($guard->mobile_number) ? $guard->mobile_number : '' }}" placeholder="@lang('cruds.guard.fields.mobile')" />
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.guard.fields.security_pin') }}<span class="required">*</span></label>
            <input type="password" class="form-control" id="security_pin" name="security_pin" value="{{ isset($guard) && !empty($guard->security_pin) ? $guard->security_pin : '' }}" placeholder="@lang('cruds.guard.fields.security_pin')" {{ isset($guard) ? 'disabled' : '' }} {{ isset($guard) ? 'readonly' : '' }} />
            @if (isset($guard)&& isset($guard->mobile_number))
            <div class="text-end align-right-btn"><a href="#" class="resetPasswordOtp" id="resetPasswordOtp">{{trans('global.reset_password')}}</a>
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
            <label for="society_id" class="form-label">@lang('cruds.guard.fields.society')<span class="required">*</span></label>
            <select id="society" name="society_id" class="form-control h-auto society_id">
                <option value="">@lang('global.select') @lang('cruds.guard.fields.society')</option>
                @foreach ($models['societies'] as $key => $val)
                <option value="{{ $val->id }}"
                    {{ isset($guard) && !empty($guard->society_id) && $guard->society_id == $val->id ? 'Selected' : '' }}
                    data-society_id="{{ $val->id }}">{{ $val->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="building" class="form-label">@lang('cruds.resident_vehicle.fields.building')</label>
            <select id="building" name="building_id" class="form-control h-auto building_id">
                <option value="">@lang('global.select') @lang('cruds.resident_vehicle.fields.building')</option>
                @if(isset($models['buildings']) && $models['buildings']->isNotEmpty())
                @foreach($models['buildings'] as $key => $val)
                <option value="{{ $val->id }}" {{ isset($guard) && !empty($guard->building_id) && $guard->building_id == $val->id ? 'selected' : '' }} data-building_id="{{ $val->id }}">
                    {{ $val->title }}
                </option>
                @endforeach
                @endif
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="unit_id" class="form-label">@lang('cruds.resident_vehicle.fields.unit')</label>
            <select id="unit" name="unit_id" class="form-control h-auto unit_id">
                <option value="">@lang('global.select') @lang('cruds.resident_vehicle.fields.unit')</option>
                @if(!empty($models['units']) && $models['units']->isNotEmpty())
                @foreach ($models['units'] as $key => $val)
                <option value="{{ $val->id }}"
                    {{ isset($guard) && !empty($guard->unit_id) && $guard->unit_id == $val->id ? 'Selected' : '' }}
                    data-unit_id="{{ $val->id }}">{{ $val->title }}</option>
                @endforeach
                @endif
            </select>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.guard.fields.note') }}</label>
            <textarea name="description" id="description" cols="30" rows="10" class="form-control" placeholder="{{ trans('cruds.guard.fields.note') }}">{{ isset($guard) ? $guard->description : '' }}</textarea>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="form-group">
            <input id="is_enabled" type="checkbox" name="status" {{ isset($guard) && !empty($guard->status) && ($guard->status == 1) ? 'checked' : (!isset($guard) ? 'checked' : '') }} />
            <label for="status" class="form-label">{{ trans('cruds.user.fields.is_enabled') }}</label>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn" type="submit">{{ isset($guard) && !empty($guard) ? trans('global.update') : trans('global.save') }} </button>
            <a href="{{ route('admin.guards.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>