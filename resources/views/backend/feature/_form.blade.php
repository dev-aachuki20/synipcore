<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <label for="title" class="form-label">{{ trans('cruds.feature.fields.title') }}<span
                    class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-control"
                value="{{ isset($feature) && !empty($feature->title) ? $feature->title : '' }}"
                placeholder="{{ trans('cruds.feature.fields.title') }}">
        </div>
    </div>
    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn"
                type="submit">{{ isset($feature) && !empty($feature) ? trans('global.update') : trans('global.save') }}
            </button>
            <a href="{{ route('admin.features.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>
