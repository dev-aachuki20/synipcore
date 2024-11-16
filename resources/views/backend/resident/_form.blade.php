<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.resident.fields.name') }}<span class="required">*</span></label>
            <input type="text" id="name" class="form-control" name="name" value="{{ isset($resident) && !empty($resident->name) ? $resident->name : '' }}" placeholder="@lang('cruds.resident.fields.name')">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.resident.fields.email') }}<span class="required">*</span></label>
            <input type="text" class="form-control" name="email" autocomplete="off" value="{{ isset($resident) && !empty($resident->email) ? $resident->email : '' }}" placeholder="@lang('cruds.resident.fields.email')" />
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.user.fields.mobile') }}<span class="required">*</span></label>
            <input type="number" class="form-control" name="mobile_number" value="{{ isset($resident) && !empty($resident->mobile_number) ? $resident->mobile_number : '' }}" placeholder="@lang('cruds.resident.fields.mobile')" />
        </div>
    </div>

    @if (!isset($resident))
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.user.fields.password') }}<span class="required">*</span></label>
            <div class="input-group">
                <input type="password" class="form-control" name="password" autocomplete="off" placeholder="{{ trans('cruds.user.fields.password') }}" />
                <div class="input-group-text toggle-password show-password">
                    <span class="password-eye"></span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="col-lg-6">
        <div class="form-group">
            <label for="type" class="form-label">@lang('cruds.resident.fields.type')<span class="required">*</span></label>
            <select id="type" name="type" class="form-control h-auto">
                <option value="">@lang('global.select') @lang('cruds.resident.fields.type')</option>
                @foreach(config('constant.resident_types') as $key => $val)
                <option value="{{ $key }}" {{ isset($resident) && !empty($resident->resident_type) && ($resident->resident_type == $key) ? 'Selected' : ''}}>{{ $val }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="society_id" class="form-label">@lang('cruds.resident.fields.society')<span class="required">*</span></label>

            {{-- @if(!empty($resident) && $resident->is_sub_admin)
             <input type="hidden" name="society_id" class="form-control" value="{{ $resident->society->id ?? '' }}">
            <input type="text" class="form-control"
                value="{{ $resident->society->name ?? '' }}"
                placeholder="@lang('cruds.resident.fields.society')" disabled readonly>
            @else --}}

            <select id="society" name="society_id" class="form-control h-auto society_id">
                <option value="">@lang('global.select') @lang('cruds.resident.fields.society')</option>
                @foreach($models['societies'] as $key=>$val)
                <option value="{{$val->id}}" {{ isset($resident) && !empty($resident->society_id) && ($resident->society_id == $val->id) ? 'Selected' : ''}} data-society_id="{{$val->id}}">{{$val->name}}</option>
                @endforeach
            </select>

            {{-- @endif --}}
        </div>
    </div>


    <div class="col-lg-6">
        <div class="form-group">
            <label for="building" class="form-label">@lang('cruds.resident.fields.building')<span class="required">*</span></label>

            <select id="building" name="building_id" class="form-control h-auto building_id">
                <option value="">@lang('global.select') @lang('cruds.resident.fields.building')</option>
                @if(!empty($models['buildings']) && $models['buildings']->isNotEmpty())
                @foreach($models['buildings'] as $key => $val)
                <option value="{{ $val->id }}" {{ isset($resident) && !empty($resident->building_id) && $resident->building_id == $val->id ? 'selected' : '' }} data-building_id="{{ $val->id }}">
                    {{ $val->title }}
                </option>
                @endforeach
                @endif
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="unit_id" class="form-label">@lang('cruds.resident.fields.unit')<span class="required">*</span></label>
            <select id="unit" name="unit_id" class="form-control h-auto unit_id">
                <option value="">@lang('global.select') @lang('cruds.resident.fields.unit')</option>
                @if(!empty($models['units']) && $models['units']->isNotEmpty())
                @foreach($models['units'] as $key=>$val)
                <option value="{{$val->id}}" {{ isset($resident) && !empty($resident->unit_id) && ($resident->unit_id == $val->id) ? 'selected' : ''}} data-unit_id="{{$val->id}}">{{$val->title}}</option>
                @endforeach
                @endif
            </select>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.resident.fields.note') }}</label>
            <textarea name="description" id="description" cols="30" rows="10" class="form-control" placeholder="{{ trans('cruds.resident.fields.note') }}">{{ isset($resident) ? $resident->description : '' }}</textarea>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="form-group">
            <input id="is_verified" type="checkbox" name="is_verified" {{ isset($resident) && !empty($resident->is_verified) && ($resident->is_verified == 1) ? 'checked' : '' }} />
            <label for="is_verified" class="form-label">{{ trans('cruds.resident.fields.is_approved') }}</label>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn" type="submit">{{ isset($resident) && !empty($resident) ? trans('global.update') : trans('global.save') }} </button>
            <a href="{{ route('admin.residents.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>