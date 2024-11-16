<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label for="title" class="form-label">@lang('cruds.service.fields.title')<span class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-control"
                placeholder="{{ trans('cruds.service.fields.title') }}"
                value="{{ isset($service) && !empty($service->title) ? $service->title : '' }}"
                onkeyup="createSlug(this)">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group input-slug">
            <label class="form-label">@lang('cruds.service.fields.slug')<span class="required">*</span> <span class="label-tooltip"
                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ trans('global.slug_msg') }}"><i
                        class="ri-information-line"></i></span></label>
            <input type="text" id="slug" name="slug" class="form-control"
                value="{{ isset($service) && !empty($service->slug) ? $service->slug : '' }}"
                placeholder="{{ trans('cruds.service.fields.slug') }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="service_category_id" class="form-label">{{ trans('cruds.service_category.title') }}<span class="required">*</span></label>
            <select name="service_category_id" class="form-control">
                <option value="">{{ trans('global.select') }}</option>
                @foreach ($serviceCategories as $category)
                <option value="{{ $category->id }}" {{ isset($service) && $service->service_category_id == $category->id ? 'selected' : '' }}>{{ ucfirst($category->title) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.service.fields.provider') }}<span class="required">*</span></label>
            <select name="user_id" class="select2 form-control select2-multiple" data-placeholder="{{ trans('global.choose') }}">
                <option value="">{{ trans('global.select') }}</option>
                @foreach ($providers as $provider)
                <option value="{{ $provider->id }}"
                    {{ isset($service) && $service->user_id == $provider->id ? 'selected' : '' }}>
                    {{ ucfirst($provider->name) }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="sort_order" class="form-label">@lang('cruds.service.fields.sort_order')<span class="required">*</span></label>
            <input type="number" id="sort_order" name="sort_order" class="form-control"
                value="{{ isset($service) && !empty($service->sort_order) ? $service->sort_order : '1' }}"
                placeholder="Sort Order" placeholder="" min="1" onkeyup="validateMinValue(this)">
            <div id="sort_order_error" class="error text-danger"></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="service_url" class="form-label">{{ trans('cruds.service.title_singular') }} {{ trans('global.url') }}</label>
            <input type="url" name="service_url" id="service_url" class="form-control" placeholder="https://example.com" value="{{ old('service_url', $service->service_url ?? '') }}">
        </div>
    </div>
    <div class="col-lg-12">
        <div class="form-group">
            <label for="image" class="form-label">@lang('cruds.service.fields.image')</label>
            <div class="UploadBtn">
                <input type="file" name="image" class="form-control fileInputBoth" accept="image/*"
                    onchange="document.getElementById('imagePreview').src = window.URL.createObjectURL(this.files[0])">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="19" viewBox="0 0 24 19" fill="none"
                    class="me-1">
                    <path
                        d="M19.9963 6.24269C18.8871 1.82389 14.4058 -0.859034 9.98705 0.250189C6.53386 1.11705 4.02646 4.10158 3.76811 7.65249C1.31621 8.05684 -0.343691 10.3723 0.0606558 12.8242C0.420101 15.0039 2.30886 16.6003 4.51803 16.5915H8.26762V15.0917H4.51803C2.86137 15.0917 1.51836 13.7487 1.51836 12.092C1.51836 10.4353 2.86137 9.09234 4.51803 9.09234C4.93222 9.09234 5.26795 8.75661 5.26795 8.34242C5.2642 4.6149 8.28295 1.59011 12.0105 1.5864C15.2372 1.58317 18.0148 3.86433 18.639 7.03006C18.7006 7.34615 18.9571 7.58763 19.2764 7.63C21.3266 7.92195 22.7519 9.8206 22.46 11.8707C22.1978 13.7117 20.626 15.0824 18.7665 15.0917H15.7668V16.5915H18.7665C21.6657 16.5828 24.0088 14.2254 24 11.3262C23.9927 8.91287 22.3408 6.8154 19.9963 6.24269Z"
                        fill="#6c757d" />
                    <path
                        d="M11.485 9.30988L8.48535 12.3096L9.54274 13.3669L11.2675 11.6496V18.8413H12.7674V11.6496L14.4847 13.3669L15.5421 12.3096L12.5424 9.30988C12.2499 9.0191 11.7775 9.0191 11.485 9.30988Z"
                        fill="#6c757d" />
                </svg> {{ trans('global.upload_file') }}
            </div>
            <div class="img-prevarea mt-3">
                <img src="{{ isset($service) && $service->service_image_url ? $service->service_image_url : asset(config('constant.default.user_icon')) }}"
                    id="imagePreview" width="100px" height="100px">
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.service.fields.description') }}</label>
            <textarea name="description" id="description" cols="30" rows="10" class="form-control" placeholder="{{ trans('cruds.service.fields.description') }}">{{ isset($service) ? $service->description : '' }}</textarea>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <input id="is_featured" type="checkbox" name="is_featured" {{ isset($service) && !empty($service->is_featured) && ($service->is_featured == 1) ? 'checked' : '' }} />
            <label for="is_featured" class="form-label">{{ trans('cruds.service.fields.feature') }}</label>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn"
                type="submit">{{ isset($service) && !empty($service) ? trans('global.update') : trans('global.save') }}
            </button>
            <a href="{{ route('admin.services.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>