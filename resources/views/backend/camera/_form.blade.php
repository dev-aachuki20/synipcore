<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.camera.fields.camera_id') }}<span class="required">*</span></label>
            <input type="text" class="form-control" name="camera_id" value="{{ isset($camera) && !empty($camera->camera_id) ? $camera->camera_id : '' }}" placeholder="@lang('cruds.camera.fields.camera_id')" />
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.camera.fields.lacated_at') }}</label>
            <input type="text" class="form-control" name="lacated_at" value="{{ isset($camera) && !empty($camera->lacated_at) ? $camera->lacated_at : '' }}" placeholder="@lang('cruds.camera.fields.lacated_at')" />
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="society_id" class="form-label"> {{ trans('cruds.camera.fields.society') }}<span
                    class="required">*</span></label>
            @if(!empty($user) && $user->is_sub_admin)
            <input type="hidden" name="society_id" class="form-control" value="{{ $user->society->uuid ?? '' }}">
            <input type="text" class="form-control" value="{{ $user->society->name ?? '' }}" placeholder="@lang('cruds.guard.fields.society')" disabled readonly>
            @else
            <select id="society_id" name="society_id" class="form-control h-auto society_id">
                <option value="">{{ trans('global.select') }}
                    {{ trans('cruds.camera.fields.society') }}
                </option>
                @foreach ($societies as $key => $society)
                <option value="{{ $key }}"
                    {{ isset($camera) && !empty($camera->society_id) && $camera->society->uuid == $key ? 'selected' : '' }}>
                    {{ $society }}
                </option>
                @endforeach
            </select>
            @endif
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="building_id" class="form-label"> {{ trans('cruds.camera.fields.building') }}</label>
            <select id="building_id" name="building_id" class="form-control h-auto building_id">
                <option value="">{{ trans('global.select') }}
                    {{ trans('cruds.camera.fields.building') }}
                </option>
                @if(!empty($user) && $user->is_sub_admin)
                @if(isset($buildings))
                @foreach($buildings as $key => $building)
                <option value="{{ $key }}"
                    {{ isset($camera) && !empty($camera->building_id) && $camera->building->uuid == $key  ? 'selected' : '' }}>
                    {{ $building }}
                </option>
                @endforeach
                @endif
                @else
                @if (isset($camera))
                @foreach ($buildings as $key => $building)
                <option value="{{ $key }}"
                    {{ isset($camera) && !empty($camera->building_id) && $camera->building->uuid == $key ? 'selected' : '' }}>
                    {{ $building }}
                </option>
                @endforeach
                @endif
                @endif
            </select>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="unit_id" class="form-label"> {{ trans('cruds.camera.fields.unit') }}</label>
            <select id="unit_id" name="unit_id" class="form-control h-auto unit_id">
                <option value="">{{ trans('global.select') }}
                    {{ trans('cruds.camera.fields.unit') }}
                </option>
                @if (isset($camera))
                @foreach ($units as $unitKey => $unit)
                <option value="{{ $unitKey }}"
                    {{ isset($camera) && !empty($camera->unit_id) && $camera->unit->uuid == $unitKey ? 'selected' : '' }}>
                    {{ $unit }}
                </option>
                @endforeach
                @endif
            </select>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn" type="submit">{{ isset($camera) && !empty($camera) ? trans('global.update') : trans('global.save') }} </button>
            <a href="{{ route('admin.cameras.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>