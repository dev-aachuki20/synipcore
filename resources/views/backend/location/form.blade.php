<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label for="title" class="form-label">@lang('cruds.location.fields.title')<span class="required">*</span> </label>
            <input type="text" id="title" name="title" class="form-control" placeholder="@lang('cruds.location.fields.title')"
                value="{{ isset($location) && !empty($location->title) ? $location->title : '' }}"
                onkeyup="createSlug(this)">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="slug" class="form-label">@lang('cruds.location.fields.slug')<span class="required">*</span> <span
                    class="label-tooltip" data-bs-toggle="tooltip" data-bs-placement="top"
                    data-bs-title="{{ trans('global.slug_msg') }}"><i class="ri-information-line"></i></span></label>
            <input type="text" id="slug" name="slug" class="form-control"
                value="{{ isset($location) && !empty($location->slug) ? $location->slug : '' }}"
                placeholder="{{ trans('cruds.location.fields.slug') }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="scope_id" class="form-label">@lang('cruds.location.fields.scope')<span class="required">*</span></label>
            <select id="scope_id" name="scope_id" class="form-control h-auto">
                @forelse (config('constant.location_scope') as $key => $value)
                    <option value="{{ $key }}"
                        {{ isset($location) && !empty($location->scope_id) && $location->scope_id == $key ? 'Selected' : '' }}>
                        {{ $value }}</option>
                @empty
                    <option value="">No Data Found!</option>
                @endforelse
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="sort_order" class="form-label">@lang('cruds.location.fields.sort_order')<span class="required">*</span></label>
            <input type="number" id="sort_order" name="sort_order" class="form-control"
                value="{{ isset($location) && !empty($location->sort_order) ? $location->sort_order : '1' }}"
                placeholder="Sort Order" placeholder="" value="1" min="1" onkeyup="validateMinValue(this)">
            <div id="sort_order_error" class="error text-danger"></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="parent_id" class="form-label">@lang('cruds.location.fields.parent')</label>
            <select id="parent_id" name="parent_id" class="form-control h-auto">
                <option value="">Root</option>
                @forelse ($parentLocation as $item)
                    @php
                        $isSelected =
                            isset($location) && !empty($location->parent_id) && $location->parent_id == $item->id
                                ? 'selected'
                                : '';
                    @endphp
                    <option value="{{ $item->uuid }}" {{ $isSelected }}>
                        {{ $item->title }}
                    </option>
                @empty
                @endforelse
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="image" class="form-label">@lang('cruds.location.fields.image')</label>
            <div class="UploadBtn">
                <input type="file" id="image" name="image" class="form-control fileInputBoth" accept="image/*"
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
                <img src="{{ isset($location) && $location->location_image_url ? $location->location_image_url : asset(config('constant.default.user_icon')) }}"
                    id="imagePreview" width="100px" height="100px">
            </div>
        </div>
    </div>
    {{-- <div class="col-lg-12">
        <h5 class="inner_sub_title">@lang('cruds.location.fields.meta_fields')</h5>
    </div>
    <div id="metaFormContainer">
        @if (isset($location) && !empty($location))
            @foreach ($location->metafieldKeysValue as $meta)
                <div class="row metaFieldsRow">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">@lang('cruds.location.fields.meta_key')</label>
                            <input type="text" name="key[]" class="form-control" value="{{ $meta['key'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">@lang('cruds.location.fields.meta_value')</label>
                            <select id="value" name="value[]" class="form-control h-auto">
                                @foreach (config('constant.location_meta_keys.value') as $key => $val)
                                    <option value="{{ $key }}" {{ $key == $meta['value'] ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach                                
                            </select>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
        <div class="row metaFieldsRow">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">@lang('cruds.location.fields.meta_key')</label>
                    <input type="text" name="key[]" class="form-control" value="">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">@lang('cruds.location.fields.meta_value')</label>
                    <select id="value" name="value[]" class="form-control h-auto">
                        @foreach (config('constant.location_meta_keys.value') as $key => $val)
                            <option value="{{ $key }}">{{ $val }}</option>
                        @endforeach                                
                    </select>
                </div>
            </div>
        </div>
        @endif
    </div>
    <div id="metaFormContainerContainer"></div>
    <div class="row">
        <div class="col-12 mb-4">
            <button type="button" data-count="0" id="addMetaFields" class="btn btn-primary addMetaFields">@lang('cruds.location.fields.add_metafields')</button>
        </div>
    </div> --}}
    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn"
                type="submit">{{ isset($service) && !empty($service) ? trans('global.update') : trans('global.save') }}
            </button>
            <a href="{{ route('admin.locations.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>
