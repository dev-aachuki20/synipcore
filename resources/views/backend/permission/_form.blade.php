<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.permission.fields.name') }}</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ isset($permission) ? $permission->name : '' }}">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.permission.fields.title') }}</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ isset($permission) ? $permission->title : '' }}">
        </div>
    </div>
    <div class="col-lg-12">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.permission.fields.route_name') }}</label>
            <input type="text" name="route_name" id="route_name" class="form-control" value="{{ isset($permission) ? $permission->route_name : '' }}">
        </div>
    </div>
    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button type="submit"
                class="btn btn-primary submitBtn">{{ !isset($permission) ? trans('global.save') : trans('global.update') }}</button>
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>
