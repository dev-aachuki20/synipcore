<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label for="name" class="form-label">@lang('cruds.resident_daily_help.fields.name')<span class="required">*</span></label>
            <input type="text" name="name" id="name" class="form-control"
                placeholder="{{ trans('cruds.resident_daily_help.fields.name') }}"
                value="{{ isset($residentDailyHelp) && !empty($residentDailyHelp->name) ? $residentDailyHelp->name : '' }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="help_type" class="form-label">@lang('cruds.resident_daily_help.fields.help_type')<span class="required">*</span></label>
            <input type="text" name="help_type" id="help_type" class="form-control"
                placeholder="{{ trans('cruds.resident_daily_help.fields.help_type') }}"
                value="{{ isset($residentDailyHelp) && !empty($residentDailyHelp->help_type) ? $residentDailyHelp->help_type : '' }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="phone_number" class="form-label">@lang('cruds.resident_daily_help.fields.contact')<span class="required">*</span></label>
            <input type="text" name="phone_number" id="phone_number" class="form-control"
                placeholder="{{ trans('cruds.resident_daily_help.fields.contact') }}"
                value="{{ isset($residentDailyHelp) && !empty($residentDailyHelp->phone_number) ? $residentDailyHelp->phone_number : '' }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="society_id" class="form-label"> {{ trans('cruds.resident_daily_help.fields.society') }}<span class="required">*</span></label>
            @if(!empty($user) && $user->is_sub_admin)
            <input type="hidden" name="society_id" class="form-control" value="{{ $user->society->uuid ?? '' }}">
            <input type="text" class="form-control"
                value="{{ $user->society->name ?? '' }}"
                placeholder="@lang('cruds.resident_vehicle.fields.society')" disabled readonly>
            @else
            <select id="society_id" name="society_id" class="form-control h-auto society_id">
                <option value="">{{ trans('global.select') }}
                    {{ trans('cruds.resident_daily_help.fields.society') }}
                </option>
                @foreach ($societies as $key => $society)
                <option value="{{ $key }}"
                    {{ isset($residentDailyHelp) && !empty($residentDailyHelp->society_id) && $residentDailyHelp->society->uuid == $key ? 'selected' : '' }}>
                    {{ $society }}
                </option>
                @endforeach
            </select>
            @endif
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="building_id" class="form-label"> {{ trans('cruds.resident_daily_help.fields.building') }}</label>
            <select id="building_id" name="building_id" class="form-control h-auto building_id">
                <option value="">{{ trans('global.select') }}
                    {{ trans('cruds.resident_daily_help.fields.building') }}
                </option>
                @if(!empty($user) && $user->is_sub_admin)
                    @if(isset($buildings))
                        @foreach($buildings as $key => $building)
                            <option value="{{ $key }}" 
                                {{ isset($residentDailyHelp) && !empty($residentDailyHelp->building_id) && $residentDailyHelp->building->uuid == $key  ? 'selected' : '' }}>
                                {{ $building }}
                            </option>
                        @endforeach
                    @endif
                @else
                    @if(isset($residentDailyHelp))
                    @foreach ($buildings as $key => $building)
                    <option value="{{ $key }}"
                        {{ isset($residentDailyHelp) && !empty($residentDailyHelp->building_id) && $residentDailyHelp->building->uuid == $key ? 'selected' : '' }}>
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
            <label for="unit_id" class="form-label"> {{ trans('cruds.resident_daily_help.fields.unit') }}</label>
            <select id="unit_id" name="unit_id" class="form-control h-auto unit_id">
                <option value="">{{ trans('global.select') }}
                    {{ trans('cruds.resident_daily_help.fields.unit') }}
                </option>
                @if (isset($residentDailyHelp))
                @foreach ($units as $unitKey => $unit)
                <option value="{{ $unitKey }}"
                    {{ isset($residentDailyHelp) && !empty($residentDailyHelp->unit_id) && $residentDailyHelp->unit->uuid == $unitKey ? 'selected' : '' }}>
                    {{ $unit }}
                </option>
                @endforeach
                @endif
            </select>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn"
                type="submit">{{ isset($residentDailyHelp) && !empty($residentDailyHelp) ? trans('global.update') : trans('global.save') }}
            </button>
            <a href="{{ route('admin.resident-daily-helps.index') }}"
                class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>