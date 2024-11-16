<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.visitor.fields.type') }}<span class="required">*</span></label>
            <select name="visitor_type" class="form-control" id="visitor-type">
                <option value="">{{ trans('global.select') }} {{ trans('cruds.visitor.fields.type') }}</option>
                @foreach (config('constant.visitor_types') as $key => $type)
                <option value="{{ $key }}">
                    {{ $type }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-6" id="society">
        <div class="form-group">
            <label for="society_id" class="form-label">@lang('cruds.visitor.fields.society')<span class="required">*</span></label>
            <select id="society" name="society_id" class="form-control h-auto society_id">
                <option value="">@lang('global.select') @lang('cruds.visitor.fields.society')</option>
                @foreach($models['societies'] as $key=> $val)
                <option value="{{$val->id}}" data-society_id="{{$val->id}}">{{$val->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-6" id="building">
        <div class="form-group">
            <label for="building" class="form-label">@lang('cruds.visitor.fields.building')<span class="required">*</span></label>
            <select id="building" name="building_id" class="form-control h-auto building_id">
                <option value="">@lang('global.select') @lang('cruds.visitor.fields.building')</option>
            </select>
        </div>
    </div>
    <div class="col-lg-6" id="unit">
        <div class="form-group">
            <label for="unit_id" class="form-label">@lang('cruds.visitor.fields.unit')<span class="required">*</span></label>
            <select id="unit" name="unit_id" class="form-control h-auto unit_id">
                <option value="">@lang('global.select') @lang('cruds.visitor.fields.unit')</option>
            </select>
        </div>
    </div>
    <div class="col-lg-6" id="name">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.visitor.fields.name') }}<span class="required">*</span></label>
            <input type="text" class="form-control" name="name" placeholder="{{ trans('cruds.visitor.fields.name') }}" />
        </div>
    </div>
    <div class="col-lg-6" id="phone-number">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.visitor.fields.contact') }}</label>
            <div class="form-group d-flex align-items-center number_prefix m-0">
                <input type="number" class="form-control" name="phone_number" placeholder="{{ trans('cruds.visitor.fields.contact') }}" />
            </div>
        </div>
    </div>
    <div class="col-lg-6" id="cab-number">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.visitor.fields.cab_number') }}</label>
            <div class="form-group d-flex align-items-center number_prefix m-0">
                <input type="number" class="form-control" name="cab_number" placeholder="{{ trans('cruds.visitor.fields.cab_number') }}" />
            </div>
        </div>
    </div>
    <div class="col-lg-6" id="keep-package">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.visitor.fields.keep_package') }}</label>
            <div class="form-group d-flex align-items-center number_prefix m-0">
                <input type="number" class="form-control" name="keep_package" placeholder="{{ trans('cruds.visitor.fields.keep_package') }}" />
            </div>
        </div>
    </div>
    <div class="col-lg-12" id="visitor-note">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.visitor.fields.visitor_note') }}</label>
            <textarea name="visitor_note" id="visitor_note" cols="30" rows="10" class="form-control" placeholder="{{ trans('cruds.visitor.fields.visitor_note') }}"></textarea>
        </div>
    </div>
    <div class="col-lg-12" id="other-info">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.visitor.fields.other_info') }}</label>
            <textarea name="other_info" id="other_info" cols="30" rows="10" class="form-control" placeholder="{{ trans('cruds.visitor.fields.other_info') }}"></textarea>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button type="submit"
                class="btn btn-success submitBtn">{{trans('global.save')}}</button>
            <a href="{{ route('admin.visitors.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>