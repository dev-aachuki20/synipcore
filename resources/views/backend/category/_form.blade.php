<div class="row">

    <div class="col-lg-12">
        <div class="form-group">
            <label for="title" class="form-label">{{ trans('cruds.category.fields.title') }}<span
                    class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-control"
                value="{{ isset($category) && !empty($category->title) ? $category->title : '' }}"
                placeholder="{{ trans('cruds.category.fields.title') }}">
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <label for="description" class="form-label">{{ trans('cruds.category.fields.description') }}</label>
            <textarea name="description" id="description" class="form-control" placeholder="{{ trans('cruds.category.fields.description') }}">{{ isset($category) && !empty($category->description) ? $category->description : '' }}</textarea>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn"
                type="submit">{{ isset($category) && !empty($category) ? trans('global.update') : trans('global.save') }}
            </button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>
