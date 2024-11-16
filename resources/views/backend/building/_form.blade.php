<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <label for="title" class="form-label">@lang('cruds.building.fields.title')<span class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-control" value="{{ isset($building) && !empty($building->title) ? $building->title : '' }}" placeholder="@lang('cruds.building.fields.title')">
        </div>
    </div>
    <div class="col-lg-12">
        <div class="form-group">
            <label for="address" class="form-label">@lang('cruds.building.fields.society')</label>
            @if(!empty($user) && $user->is_sub_admin)
            <input type="hidden" name="society_id" class="form-control" value="{{ $user->society->id ?? '' }}">
            <input type="text" class="form-control"
                value="{{ $user->society->name ?? '' }}"
                placeholder="@lang('cruds.building.fields.society')" disabled readonly>
            @else
            <select id="society" name="society_id" class="form-control h-auto">
                <option value="">@lang('global.select') @lang('cruds.building.fields.society')</option>
                @forelse($society as $key=>$val)
                <option value="{{$val->id}}" {{ isset($building) && !empty($building->society_id) && ($building->society_id == $val->id) ? 'Selected' : ''}}>{{$val->name}}</option>
                @empty
                @endforelse
            </select>
            @endif
        </div>
    </div>
    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn" type="submit">{{ isset($building) && !empty($building) ? trans('global.update') : trans('global.save') }} </button>
            <a href="{{ route('admin.buildings.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>