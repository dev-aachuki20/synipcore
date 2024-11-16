<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label for="title" class="form-label">{{ trans('cruds.amenity.fields.title') }}<span class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-control"
                value="{{ isset($amenity) && !empty($amenity->title) ? $amenity->title : '' }}"
                placeholder="{{ trans('cruds.amenity.fields.title') }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="fee" class="form-label">{{ trans('cruds.amenity.fields.fee') }}</label>
            <input type="number" id="fee" name="fee" class="form-control"
                value="{{ isset($amenity) && !empty($amenity->fee) ? $amenity->fee : '' }}"
                placeholder="{{ trans('cruds.amenity.fields.fee') }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="capacity" class="form-label">{{ trans('cruds.amenity.fields.capacity') }}<span class="required">*</span></label>
            <input type="number" id="capacity" name="capacity" class="form-control"
                value="{{ isset($amenity) && !empty($amenity->capacity) ? $amenity->capacity : '' }}"
                placeholder="{{ trans('cruds.amenity.fields.capacity') }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="booking_capacity" class="form-label">{{ trans('cruds.amenity.fields.booking_capacity') }}<span class="required">*</span></label>
            <input type="number" id="booking_capacity" name="booking_capacity" class="form-control"
                value="{{ isset($amenity) && !empty($amenity->booking_capacity) ? $amenity->booking_capacity : '' }}"
                placeholder="{{ trans('cruds.amenity.fields.booking_capacity') }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="advance_booking_days" class="form-label">{{ trans('cruds.amenity.fields.advance_booking_days') }}<span class="required">*</span></label>
            <input type="number" id="advance_booking_days" name="advance_booking_days" class="form-control"
                value="{{ isset($amenity) && !empty($amenity->advance_booking_days) ? $amenity->advance_booking_days : '' }}"
                placeholder="{{ trans('cruds.amenity.fields.advance_booking_days') }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="max_days_per_unit" class="form-label">{{ trans('cruds.amenity.fields.max_days_per_unit') }}<span class="required">*</span></label>
            <input type="number" id="max_days_per_unit" name="max_days_per_unit" class="form-control"
                value="{{ isset($amenity) && !empty($amenity->max_days_per_unit) ? $amenity->max_days_per_unit : '' }}"
                placeholder="{{ trans('cruds.amenity.fields.max_days_per_unit') }}">
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <label for="type" class="form-label"> {{ trans('cruds.amenity.fields.society') }}<span class="required">*</span></label>
            @if(!empty($user) && $user->is_sub_admin)
                <input type="hidden" name="society_id" class="form-control" value="{{ $user->society->uuid ?? '' }}">
                <input type="text" class="form-control"
                    value="{{ $user->society->name ?? '' }}"
                    placeholder="@lang('cruds.amenity.fields.society')" disabled readonly>
            @else
                <select id="type" name="society_id" class="form-control h-auto">
                    <option value="">{{ trans('global.select') }}</option>
                    @foreach ($societies as $key => $society)
                        <option value="{{ $key }}" {{ isset($amenity) && !empty($amenity->society_id) && $amenity->society->uuid == $key ? 'selected' : '' }}>
                            {{ $society }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>
    </div>
    <div class="col-lg-12">
        <div class="form-group">
            <label for="description" class="form-label">{{ trans('cruds.amenity.fields.description') }}</label>
            <textarea class="form-control" id="description" name="description" cols="30" rows="5"
                placeholder="{{ trans('cruds.amenity.fields.description') }}">{{ isset($amenity) && !empty($amenity->description) ? $amenity->description : '' }}</textarea>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn"
                type="submit">{{ isset($amenity) && !empty($amenity) ? trans('global.update') : trans('global.save') }}
            </button>
            <a href="{{ route('admin.amenities.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>
