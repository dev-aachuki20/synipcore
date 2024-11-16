<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <label for="title" class="form-label">@lang('cruds.unit.fields.title')<span class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-control" value="{{ isset($unit) && !empty($unit->title) ? $unit->title : '' }}" placeholder="@lang('cruds.unit.fields.title')">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="address" class="form-label">@lang('cruds.unit.fields.society')</label>
            @if(!empty($user) && $user->is_sub_admin)
            <input type="hidden" name="society_id" class="form-control" value="{{ $user->society->id ?? '' }}">
            <input type="text" class="form-control"
                value="{{ $user->society->name ?? '' }}"
                placeholder="@lang('cruds.unit.fields.society')" disabled readonly>
            @else
            <select id="society" name="society_id" class="form-control h-auto society_id">
                <option value="">@lang('global.select') @lang('cruds.unit.fields.society')</option>
                @foreach($society as $key=>$val)
                <option value="{{$val->id}}" {{ isset($unit) && !empty($unit->society_id) && ($unit->society_id == $val->id) ? 'Selected' : ''}} data-society_id="{{$val->id}}">{{$val->name}}</option>
                @endforeach
            </select>
            @endif
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="city" class="form-label">@lang('cruds.unit.fields.building')</label>
            <select id="building" name="building_id" class="form-control h-auto building_id">
                <option value="">@lang('global.select') @lang('cruds.unit.fields.building')</option>
                @if(!empty($user) && $user->is_sub_admin)
                    @if(isset($buildings) && $buildings->isNotEmpty())
                        @foreach($buildings as $key => $val)
                        <option value="{{ $val->id }}" {{ isset($unit) && !empty($unit->building_id) && $unit->building_id == $val->id ? 'selected' : '' }}>
                            {{ $val->title }}
                        </option>
                        @endforeach
                    @endif
                @else
                    @if(isset($unit) && !empty($unit))
                        @foreach($buildings as $key=>$val)
                        <option value="{{$val->id}}" {{ isset($unit) && !empty($unit->building_id) && ($unit->building_id == $val->id) ? 'selected' : ''}}>{{$val->title}}</option>
                        @endforeach
                    @endif
                @endif
            </select>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn" type="submit">{{ isset($unit) && !empty($unit) ? trans('global.update') : trans('global.save') }} </button>
            <a href="{{ route('admin.units.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>