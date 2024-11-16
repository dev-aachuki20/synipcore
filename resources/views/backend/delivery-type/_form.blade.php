<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <label for="title" class="form-label">{{ trans('cruds.delivery_type.fields.title') }}<span
                    class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-control"
                value="{{ isset($deliveryType) ? $deliveryType->title : '' }}" placeholder="{{ trans('cruds.delivery_type.fields.title') }}">
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <label for="notify_user" class="form-label">@lang('cruds.delivery_type.fields.notify_user')<span
                    class="required">*</span></label></label>
            <select id="notify_user" name="notify_user" class="form-control h-auto">
                <option value="">{{trans('global.select')}}</option>
                @foreach (config('constant.notify_user') as $key => $value)
                <option value="{{ $key }}"
                    {{ isset($deliveryType) && $deliveryType->notify_user == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <label for="description" class="form-label">{{ trans('cruds.delivery_type.fields.description') }}<span
                    class="required">*</span></label>
            <textarea name="description" id="description" class="form-control" placeholder="{{ trans('cruds.delivery_type.fields.description') }}">{{ isset($deliveryType) && !empty($deliveryType->description) ? $deliveryType->description : '' }}</textarea>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="form-group">
            <input id="due_payment" type="checkbox" name="due_payment" {{ isset($deliveryType) && ($deliveryType->due_payment == 0) ? 'checked' : '' }} />
            <label for="due_payment" class="form-label">{{ trans('cruds.delivery_type.fields.due_payment') }}</label>
        </div>
    </div>



    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn"
                type="submit">{{ isset($deliveryType) && !empty($deliveryType) ? trans('global.update') : trans('global.save') }}
            </button>
            <a href="{{ route('admin.delivery-types.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>