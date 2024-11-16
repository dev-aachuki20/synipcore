<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label for="title" class="form-label">{{ trans('cruds.property_type.fields.title') }}<span
                    class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-control"
                value="{{ isset($propertyType) && !empty($propertyType->title) ? $propertyType->title : '' }}"
                placeholder="{{ trans('cruds.property_type.fields.title') }}">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="code" class="form-label">{{ trans('cruds.property_type.fields.code') }}<span
                    class="required">*</span></label>
            <input type="text" id="code" name="code" class="form-control"
                value="{{ isset($propertyType) && !empty($propertyType->code) ? $propertyType->code : '' }}"
                placeholder="{{ trans('cruds.property_type.fields.code') }}">
        </div>
    </div>

    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn"
                type="submit">{{ isset($propertyType) && !empty($propertyType) ? trans('global.update') : trans('global.save') }}
            </button>
            <a href="{{ route('admin.prpoertyTypes.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>
