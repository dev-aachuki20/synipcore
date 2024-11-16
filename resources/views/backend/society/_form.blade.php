<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label for="title" class="form-label">@lang('cruds.society.fields.title')<span class="required">*</span></label>
            <input type="text" id="title" name="name" class="form-control" value="{{ isset($society) && !empty($society->name) ? $society->name : '' }}" placeholder="@lang('cruds.society.fields.title')">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="address" class="form-label">@lang('cruds.society.fields.address')<span class="required">*</span></label>
                <input type="text" id="address" name="address" class="form-control" value="{{ isset($society) && !empty($society->address) ? $society->address : '' }}" placeholder="@lang('cruds.society.fields.address')">
        </div>
    </div>    
    <div class="col-lg-6">
        <div class="form-group">
            <label for="city" class="form-label">@lang('cruds.society.fields.city')<span class="required">*</span></label>
            <select name="city" id="city" class="form-control">
                <option value="">Select @lang('cruds.society.fields.city')</option>
                @foreach ($cities as $cityId => $city)
                    <option value="{{$cityId}}" {{ (isset($society) && $cityId == $society->city ? 'selected' : '' ) }}>{{ $city }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="district" class="form-label">@lang('cruds.society.fields.district')<span class="required">*</span></label>
            <select name="district" id="district" class="form-control">
                <option value="">{{trans('global.select')}} @lang('cruds.society.fields.district')</option>               
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="latitude" class="form-label">@lang('cruds.society.fields.latitude')<span class="required">*</span></label>
            <input type="number" id="latitude" name="latitude" class="form-control" value="{{ isset($society) && !empty($society->latitude) ? $society->latitude : '' }}" placeholder="@lang('cruds.society.fields.latitude')" step="any">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="longitude" class="form-label">@lang('cruds.society.fields.longitude')<span class="required">*</span></label>
            <input type="number" id="longitude" name="longitude" class="form-control" value="{{ isset($society) && !empty($society->longitude) ? $society->longitude : '' }}" placeholder="@lang('cruds.society.fields.longitude')" step="any">
        </div>
    </div>
    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn" type="submit">{{ isset($society) && !empty($society) ? trans('global.update') : trans('global.save') }} </button>
            <a href="{{ route('admin.societies.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>