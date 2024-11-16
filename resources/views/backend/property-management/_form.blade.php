<div class="row">
    <input type="hidden" name="property_managementIds" id="property_managementIds">

    <div class="col-lg-4">
        <div class="form-group">
            <label for="property_item" class="form-label">{{ trans('cruds.property_management.fields.item_name') }}<span
                    class="required">*</span></label>
            <input type="text" id="property_item" name="property_item" class="form-control"
                value="{{ isset($propertyManagement) && !empty($propertyManagement->property_item) ? $propertyManagement->property_item : '' }}"
                placeholder="{{ trans('cruds.property_management.fields.item_name') }}">
        </div>
    </div>

    <div class="col-lg-4">
        <div class="form-group">
            <label for="property_code" class="form-label">{{ trans('cruds.property_management.fields.property_code') }}<span
                    class="required">*</span></label>
            <input type="text" id="property_code" name="property_code" class="form-control"
                value="{{ isset($propertyManagement) && !empty($propertyManagement->property_code) ? $propertyManagement->property_code : '' }}"
                placeholder="{{ trans('cruds.property_management.fields.property_code') }}">
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label for="property_type_id" class="form-label">
                {{ trans('cruds.property_management.fields.property_type') }}<span class="required">*</span></label>
            <select id="property_type_id" name="property_type_id" class="form-control h-auto property_type_id">
                <option value="">{{ trans('global.select') }}
                    {{ trans('cruds.property_management.fields.property_type') }}
                </option>

                @foreach ($propertyTypes as $key => $type)
                <option value="{{ $type->id }}"
                    {{ isset($propertyManagement) && !empty($propertyManagement->property_type_id) && $propertyManagement->property_type_id == $type->id ? 'selected' : '' }}>
                    {{ ucfirst($type->title) }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="amount" class="form-label">{{ trans('cruds.property_management.fields.amount') }}<span
                    class="required">*</span></label>
            <input type="number" id="amount" name="amount" class="form-control"
                value="{{ isset($propertyManagement) && !empty($propertyManagement->amount) ? $propertyManagement->amount : '' }}"
                placeholder="{{ trans('cruds.property_management.fields.amount') }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="unit_price" class="form-label">{{ trans('cruds.property_management.fields.unit_price') }}<span
                    class="required">*</span></label>
            <input type="number" id="unit_price" name="unit_price" class="form-control"
                value="{{ isset($propertyManagement) && !empty($propertyManagement->unit_price) ? $propertyManagement->unit_price : '' }}"
                placeholder="{{ trans('cruds.property_management.fields.unit_price') }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="purchase_date"
                class="form-label">{{ trans('cruds.property_management.fields.purchase_date') }}<span
                    class="required">*</span></label>
            <input type="text" id="purchase_date" name="purchase_date" class="form-control"
                value="{{ isset($propertyManagement) && !empty($propertyManagement->purchase_date) ? $propertyManagement->purchase_date : '' }}"
                placeholder="{{ trans('cruds.property_management.fields.purchase_date') }}" readonly>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="society_id" class="form-label"> {{ trans('cruds.property_management.fields.society') }}<span
                    class="required">*</span></label>
            @if(!empty($user) && $user->is_sub_admin)
            <input type="hidden" name="society_id" class="form-control" value="{{ $user->society->uuid ?? '' }}">
            <input type="text" class="form-control" value="{{ $user->society->name ?? '' }}" placeholder="@lang('cruds.guard.fields.society')" disabled readonly>
            @else
            <select id="society_id" name="society_id" class="form-control h-auto society_id">
                <option value="">{{ trans('global.select') }}
                    {{ trans('cruds.property_management.fields.society') }}
                </option>
                @foreach ($societies as $key => $society)
                <option value="{{ $key }}"
                    {{ isset($propertyManagement) && !empty($propertyManagement->society_id) && $propertyManagement->society->uuid == $key ? 'selected' : '' }}>
                    {{ ucfirst($society) }}
                </option>
                @endforeach
            </select>
            @endif
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="building_id" class="form-label"> {{ trans('cruds.property_management.fields.building') }}</label>
            <select id="building_id" name="building_id" class="form-control h-auto building_id">
                <option value="">{{ trans('global.select') }}
                    {{ trans('cruds.property_management.fields.building') }}
                </option>

                @if(!empty($user) && $user->is_sub_admin)
                @if(isset($buildings))
                @foreach($buildings as $key => $building)
                <option value="{{ $building->uuid }}" {{ isset($propertyManagement) && !empty($propertyManagement->building_id) && $propertyManagement->building->uuid == $key  ? 'selected' : '' }}>
                    {{ ucfirst($building->title) }}
                </option>
                @endforeach
                @endif
                @else
                @if (isset($propertyManagement))
                @foreach ($buildings as $key => $building)
                <option value="{{ $key }}"
                    {{ isset($propertyManagement) && !empty($propertyManagement->building_id) && $propertyManagement->building->uuid == $key ? 'selected' : '' }}>
                    {{ ucfirst($building) }}
                </option>
                @endforeach
                @endif
                @endif
            </select>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="unit_id" class="form-label"> {{ trans('cruds.property_management.fields.unit') }}</label>
            <select id="unit_id" name="unit_id" class="form-control h-auto unit_id">
                @if (isset($propertyManagement))
                <option value="">{{ trans('global.select') }}
                    {{ trans('cruds.property_management.fields.unit') }}
                </option>
                @foreach ($units as $unitKey => $unit)
                <option value="{{ $unitKey }}"
                    {{ isset($propertyManagement) && !empty($propertyManagement->unit_id) && $propertyManagement->unit->uuid == $unitKey ? 'selected' : '' }}>
                    {{ ucfirst($unit) }}
                </option>
                @endforeach
                @endif
            </select>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label for="property_image" class="form-label">{{ trans('global.image') }}</label>
            <div id="property_image" class="form-control dropzone">
                <div class="dz-default dz-message">{{ trans('global.drag_drop') }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <label for="title" class="form-label">@lang('cruds.property_management.fields.description')</label>
            <textarea name="description" id="description" class="form-control" cols="30" rows="10" placeholder="@lang('cruds.property_management.fields.description')">{{ isset($propertyManagement) && !empty($propertyManagement->description) ? $propertyManagement->description : '' }}</textarea>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <label for="title" class="form-label">@lang('cruds.property_management.fields.location')</label>
            <textarea name="location" id="location" class="form-control" cols="30" rows="10" placeholder="@lang('cruds.property_management.fields.location')">{{ isset($propertyManagement) && !empty($propertyManagement->location) ? $propertyManagement->location : '' }}</textarea>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <label for="title" class="form-label">@lang('cruds.property_management.fields.allocation')</label>
            <textarea name="allocation" id="allocation" class="form-control" cols="30" rows="10" placeholder="@lang('cruds.property_management.fields.allocation')">{{ isset($propertyManagement) && !empty($propertyManagement->allocation) ? $propertyManagement->allocation : '' }}</textarea>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn"
                type="submit">{{ isset($propertyManagement) && !empty($propertyManagement) ? trans('global.update') : trans('global.save') }}
            </button>
            <a href="{{ route('admin.property-managements.index') }}"
                class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>