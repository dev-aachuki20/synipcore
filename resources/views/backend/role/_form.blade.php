<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <label class="form-label">{{ trans('cruds.role.fields.role_name') }}<span class="required">*</span></label>
            <input type="text" name="role_name" class="form-control" placeholder="Role Name"
                value="{{ isset($roleuser) ? $roleuser->name : '' }}">
        </div>
    </div>
    <div class="col-lg-12">
        <div class="form-group mt-2">
            <div class="label_wbtn">
                <label class="form-label mb-0">{{ trans('cruds.menus.permissions') }}</label>
                <div>
                    <button type="button" id="select-all" class="btn btn-primary">{{ trans('global.select_all') }}</button>
                    <button type="button" id="deselect-all" class="btn btn-secondary">{{ trans('global.deselect_all') }}</button>
                </div>
            </div>

            <select name="permissions[]" id="permissions" class="select2 form-control select2-multiple"
                data-toggle="select2" multiple="multiple" data-placeholder=" {{ trans('global.choose') }}">
                @foreach ($permissions as $permission)
                    @php
                        $getSelectedOptions = isset($roleuser) && $roleuser->permissions->contains($permission->id) ? 'selected' : '';
                    @endphp
                    <option value="{{ $permission->id }}" {{ $getSelectedOptions }}>{{ $permission->title }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button type="submit"
                class="btn btn-primary submitBtn">{{ !isset($roleuser) ? trans('global.save') : trans('global.update') }}</button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>
