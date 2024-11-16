<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <label for="title" class="form-label">{{ trans('cruds.service_category.fields.title') }}<span
                    class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-control"
                value="{{ isset($serviceCategory) && !empty($serviceCategory->title) ? $serviceCategory->title : '' }}"
                placeholder="{{ trans('cruds.service_category.fields.title') }}">
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <label for="type" class="form-label">@lang('global.status')<span class="required">*</span></label>
            <select id="type" name="status" class="form-control h-auto">
                @foreach (config('constant.status_type.service_category_status') as $key => $val)
                <option value="{{ $key }}"
                    {{ isset($serviceCategory) && !empty($serviceCategory->status) && $serviceCategory->status == $key ? 'Selected' : '' }}>
                    {{ $val }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn"
                type="submit">{{ isset($serviceCategory) && !empty($serviceCategory) ? trans('global.update') : trans('global.save') }}
            </button>
            <a href="{{ route('admin.service-categories.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>