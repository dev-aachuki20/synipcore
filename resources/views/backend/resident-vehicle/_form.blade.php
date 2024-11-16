<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label for="vehicle_number" class="form-label">@lang('cruds.resident_vehicle.fields.vehicle_number')<span class="required">*</span></label>
            <input type="text" name="vehicle_number" id="vehicle_number" class="form-control" placeholder="{{trans('cruds.resident_vehicle.fields.vehicle_number')}}"
                value="{{ isset($vehicle) && !empty($vehicle->vehicle_number) ? $vehicle->vehicle_number : '' }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="vehicle_type" class="form-label">@lang('cruds.resident_vehicle.fields.vehicle_type')<span class="required">*</span></label>
            <input type="text" name="vehicle_type" id="vehicle_type" class="form-control" placeholder="{{trans('cruds.resident_vehicle.fields.vehicle_type')}}"
                value="{{ isset($vehicle) && !empty($vehicle->vehicle_type) ? $vehicle->vehicle_type : '' }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="vehicle_model" class="form-label">@lang('cruds.resident_vehicle.fields.vehicle_model')<span class="required">*</span></label>
            <input type="text" name="vehicle_model" id="vehicle_model" class="form-control" placeholder="{{trans('cruds.resident_vehicle.fields.vehicle_model')}}"
                value="{{ isset($vehicle) && !empty($vehicle->vehicle_model) ? $vehicle->vehicle_model : '' }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="parking_slot_no" class="form-label">@lang('cruds.resident_vehicle.fields.parking_slot')<span class="required">*</span></label>
            <input type="text" name="parking_slot_no" id="parking_slot_no" class="form-control" placeholder="{{trans('cruds.resident_vehicle.fields.parking_slot')}}"
                value="{{ isset($vehicle) && !empty($vehicle->parking_slot_no) ? $vehicle->parking_slot_no : '' }}">
        </div>
    </div>

<div class="col-lg-6">
    <div class="form-group">
        <label for="society_id" class="form-label">@lang('cruds.resident_vehicle.fields.society')<span class="required">*</span></label>
        <select id="society" name="society_id" class="form-control h-auto society_id">
            <option value="">@lang('global.select') @lang('cruds.resident_vehicle.fields.society')</option>
            @foreach ($models['societies'] as $key => $val)
            <option value="{{ $val->id }}"
                {{ isset($vehicle) && !empty($vehicle->society_id) && $vehicle->society_id == $val->id ? 'Selected' : '' }}
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
            @if(!empty($models['buildings']) && $models['buildings']->isNotEmpty())
            @foreach($models['buildings'] as $key => $val)
            <option value="{{ $val->id }}" {{ isset($vehicle) && !empty($vehicle->building_id) && $vehicle->building_id == $val->id ? 'selected' : '' }} data-building_id="{{ $val->id }}">
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
                {{ isset($vehicle) && !empty($vehicle->unit_id) && $vehicle->unit_id == $val->id ? 'Selected' : '' }}
                data-unit_id="{{ $val->id }}">{{ $val->title }}</option>
            @endforeach
            @endif
        </select>
    </div>
</div>

<div class="col-lg-6">
    <div class="form-group">
        <label for="type" class="form-label">@lang('cruds.resident_vehicle.fields.status')<span class="required">*</span></label>
        <select id="type" name="status" class="form-control h-auto">
            @foreach (config('constant.status_type.vehicle_status') as $key => $val)
            <option value="{{ $key }}"
                {{ isset($vehicle) && !empty($vehicle->status) && $vehicle->status == $key ? 'Selected' : '' }}>
                {{ $val }}
            </option>
            @endforeach
        </select>
    </div>
</div>

<div class="col-lg-12">
    <div class="bottombtn-group">
        <button class="btn btn-primary submitBtn"
            type="submit">{{ isset($vehicle) && !empty($vehicle) ? trans('global.update') : trans('global.save') }}
        </button>
        <a href="{{ route('admin.resident-vehicles.index') }}"
            class="btn btn-danger">{{ trans('global.back') }}</a>
    </div>
</div>
</div>