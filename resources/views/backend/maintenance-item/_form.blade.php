<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label for="title" class="form-label">{{ trans('cruds.maintenance_item.fields.title') }}<span
                    class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-control"
                value="{{ isset($maintenanceItem) && !empty($maintenanceItem->title) ? $maintenanceItem->title : '' }}"
                placeholder="{{ trans('cruds.maintenance_item.fields.title') }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="category_id" class="form-label">@lang('cruds.maintenance_plan.fields.category')<span class="required">*</span></label>
            <select id="category_id" name="category_id" class="form-control h-auto">
                <option value="">{{ trans('global.select') }}</option>
                @foreach ($categories as $key => $category)
                <option
                    value="{{ $category->id }}" {{ isset($maintenanceItem) && $maintenanceItem->category_id == $category->id ? 'selected' : '' }}>
                    {{ $category->title }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <label for="description" class="form-label">{{ trans('cruds.maintenance_item.fields.description') }}</label>
            <textarea name="description" id="description" class="form-control" placeholder="{{ trans('cruds.maintenance_item.fields.description') }}">{{ isset($maintenanceItem) && !empty($maintenanceItem->description) ? $maintenanceItem->description : '' }}</textarea>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <label for="type" class="form-label">@lang('cruds.maintenance_item.fields.duration')<span class="required">*</span></label>
            <select id="type" name="duration" class="form-control h-auto">
                <option value="">{{ trans('global.select') }}</option>
                @foreach (config('constant.durations') as $key => $val)
                <option value="{{ $key }}"
                    {{ isset($maintenanceItem) && !empty($maintenanceItem->duration) && $maintenanceItem->duration == $key ? 'Selected' : '' }}>
                    {{ $val }}
                </option>
                @endforeach
            </select>
        </div>
    </div>


    <div class="col-lg-12">
        <div class="form-group">
            <label for="budget" class="form-label">{{ trans('cruds.maintenance_item.fields.budget') }}<span
                    class="required">*</span></label>
            <input type="number" id="budget" name="budget" class="form-control"
                value="{{ isset($maintenanceItem) && !empty($maintenanceItem->budget) ? $maintenanceItem->budget : 0 }}"
                placeholder="{{ trans('cruds.maintenance_item.fields.budget') }}">
        </div>
    </div>
    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn"
                type="submit">{{ isset($maintenanceItem) && !empty($maintenanceItem) ? trans('global.update') : trans('global.save') }}
            </button>
            <a href="{{ route('admin.maintenance-items.index') }}"
                class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>