<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label for="subject" class="form-label">{{ trans('cruds.delivery_management.fields.subject') }}<span
                    class="required">*</span></label>
            <input type="text" id="subject" name="subject" class="form-control"
                value="{{ isset($deliveryManagement) ? $deliveryManagement->subject : '' }}"
                placeholder="{{ trans('cruds.delivery_management.fields.subject') }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="delivery_type_id" class="form-label">
                {{ trans('cruds.delivery_management.fields.delivery_type') }}<span class="required">*</span></label>
            <select id="delivery_type_id" name="delivery_type_id" class="form-control h-auto delivery_type_id">
                <option value="">{{ trans('global.select') }}
                    {{ trans('cruds.delivery_management.fields.delivery_type') }}
                </option>
                @foreach ($types as $key => $type)
                <option value="{{ $key }}"
                    {{ isset($deliveryManagement) && $deliveryManagement->delivery_type_id == $key ? 'selected' : '' }}>
                    {{ $type }}
                </option>
                @endforeach
            </select>
        </div>
    </div>


    <div class="col-lg-3 society-field">
        <div class="form-group">
            <label for="society_id" class="form-label"> {{ trans('cruds.delivery_management.fields.society') }}<span
                    class="required">*</span></label>
            @if(!empty($user) && $user->is_sub_admin)
            <input type="hidden" name="society_id" class="form-control" value="{{ $user->society->uuid ?? '' }}">
            <input type="text" class="form-control" value="{{ $user->society->name ?? '' }}" placeholder="@lang('cruds.guard.fields.society')" disabled readonly>
            @else
            <select id="society_id" name="society_id" class="form-control h-auto society_id">
                <option value="">{{ trans('global.select') }}
                    {{ trans('cruds.delivery_management.fields.society') }}
                </option>
                @foreach ($societies as $key => $society)
                <option value="{{ $key }}"
                    {{ isset($deliveryManagement) && !empty($deliveryManagement->society_id) && $deliveryManagement->society->uuid == $key ? 'selected' : '' }}>
                    {{ $society }}
                </option>
                @endforeach
            </select>
            @endif
        </div>
    </div>

    <div class="col-lg-3 building-field">
        <div class="form-group">
            <label for="building_id" class="form-label"> {{ trans('cruds.delivery_management.fields.building') }}</label>
            <select id="building_id" name="building_id" class="form-control h-auto building_id">
                <option value="">{{ trans('global.select') }}
                    {{ trans('cruds.delivery_management.fields.building') }}
                </option>
                @if(!empty($user) && $user->is_sub_admin)
                @if(isset($buildings))
                @foreach($buildings as $key => $building)
                <option value="{{ $key }}"
                    {{ isset($deliveryManagement) && !empty($deliveryManagement->building_id) && $deliveryManagement->building->uuid == $key  ? 'selected' : '' }}>
                    {{ $building }}
                </option>
                @endforeach
                @endif
                @else
                @if (isset($deliveryManagement))
                @foreach ($buildings as $key => $building)
                <option value="{{ $key }}"
                    {{ isset($deliveryManagement) && !empty($deliveryManagement->building_id) && $deliveryManagement->building->uuid == $key ? 'selected' : '' }}>
                    {{ $building }}
                </option>
                @endforeach
                @endif
                @endif
            </select>
        </div>
    </div>

    <div class="col-lg-3 unit-field">
        <div class="form-group">
            <label for="unit_id" class="form-label"> {{ trans('cruds.delivery_management.fields.unit') }}</label>
            <select id="unit_id" name="unit_id" class="form-control h-auto unit_id">
                @if (isset($deliveryManagement))
                <option value="">{{ trans('global.select') }}
                    {{ trans('cruds.delivery_management.fields.unit') }}
                </option>
                @foreach ($units as $unitKey => $unit)
                <option value="{{ $unitKey }}"
                    {{ isset($deliveryManagement) && !empty($deliveryManagement->unit_id) && $deliveryManagement->unit->uuid == $unitKey ? 'selected' : '' }}>
                    {{ $unit }}
                </option>
                @endforeach
                @endif
            </select>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="form-group">
            <label for="type" class="form-label">@lang('cruds.post.fields.status')<span class="required">*</span></label>
            <select id="type" name="status" class="form-control h-auto">
                @foreach (config('constant.status_type.delivery_status') as $key => $val)
                <option value="{{ $key }}"
                    {{ isset($deliveryManagement) && !empty($deliveryManagement->status) && $deliveryManagement->status == $key ? 'Selected' : '' }}>
                    {{ $val }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <label for="message" class="form-label">{{ trans('cruds.delivery_management.fields.message') }}<span
                    class="required">*</span></label>
            <textarea name="message" id="message" class="form-control" rows="10" cols="12" placeholder="{{ trans('cruds.delivery_management.fields.message') }}">{{ isset($deliveryManagement) && !empty($deliveryManagement->message) ? $deliveryManagement->message : '' }}</textarea>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <label for="notes" class="form-label">{{ trans('cruds.delivery_management.fields.note') }}</label>
            <textarea name="notes" id="notes" class="form-control" rows="10" cols="12">{{ isset($deliveryManagement) && !empty($deliveryManagement->notes) ? $deliveryManagement->notes : '' }}</textarea>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn"
                type="submit">{{ isset($deliveryManagement) && !empty($deliveryManagement) ? trans('global.update') : trans('global.save') }}
            </button>
            <a href="{{ route('admin.delivery-managements.index') }}"
                class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>