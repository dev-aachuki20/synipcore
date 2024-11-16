<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label for="title" class="form-label">@lang('cruds.payment_method.fields.title')<span class="required">*</span></label>
            <input type="text" name="title" id="title" class="form-control" placeholder="@lang('cruds.payment_method.fields.title')"
                value="{{ isset($paymentMethod) && !empty($paymentMethod->title) ? $paymentMethod->title : '' }}" onkeyup="createSlug(this)">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="slug" class="form-label">@lang('cruds.payment_method.fields.slug')<span class="required">*</span> <span
                    class="label-tooltip" data-bs-toggle="tooltip" data-bs-placement="top"
                    data-bs-title="{{trans('global.slug_msg')}}"><i
                        class="ri-information-line"></i></span></label>
            <input type="text" id="slug" name="slug" class="form-control" placeholder="{{trans('cruds.payment_method.fields.slug')}}"
                value="{{ isset($paymentMethod) && !empty($paymentMethod->slug) ? $paymentMethod->slug : '' }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="type" class="form-label">@lang('cruds.payment_method.fields.type')</label>
            <select id="type" name="method_type" class="form-control h-auto">
                @foreach (config('constant.status_type.payment_method_type') as $key => $val)
                    <option value="{{ $key }}"
                        {{ isset($paymentMethod) && !empty($paymentMethod->method_type) && $paymentMethod->method_type == $key ? 'Selected' : '' }}>
                        {{ $val }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="type" class="form-label">@lang('cruds.payment_method.fields.status')</label>
            <select id="type" name="status" class="form-control h-auto">
                @foreach (config('constant.status_type.payment_method_status') as $key => $val)
                    <option value="{{ $key }}" {{ isset($paymentMethod) && $paymentMethod->status == $key ? 'selected' : '' }}>{{ $val }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn"
                type="submit">{{ isset($paymentMethod) && !empty($paymentMethod) ? trans('global.update') : trans('global.save') }}
            </button>
            <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>
