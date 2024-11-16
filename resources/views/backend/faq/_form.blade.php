<div class="row">

    <div class="col-lg-12">
        <div class="form-group">
            <label for="title" class="form-label">{{ trans('cruds.faq.fields.title') }}<span
                    class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-control"
                value="{{ isset($faq) && !empty($faq->title) ? $faq->title : '' }}"
                placeholder="{{ trans('cruds.faq.fields.title') }}">
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <label for="short_description" class="form-label">{{ trans('cruds.faq.fields.short_description') }}</label>
            <textarea class="form-control" id="short_description" name="short_description" cols="30" rows="5"
                placeholder="{{ trans('cruds.faq.fields.short_description') }}">{{ isset($faq) && !empty($faq->short_description) ? $faq->short_description : '' }}</textarea>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <label for="description" class="form-label">{{ trans('cruds.faq.fields.description') }}</label>
            <textarea name="description" id="description" class="form-control">{{ isset($faq) && !empty($faq->description) ? $faq->description : '' }}</textarea>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn"
                type="submit">{{ isset($faq) && !empty($faq) ? trans('global.update') : trans('global.save') }}
            </button>
            <a href="{{ route('admin.faqs.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>
