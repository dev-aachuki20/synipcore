<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label for="title" class="form-label">{{ trans('cruds.payment_request.fields.title') }}<span class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-control" value="{{ isset($paymentRequest) && !empty($paymentRequest->title) ? $paymentRequest->title : '' }}" placeholder="{{ trans('cruds.payment_request.fields.title') }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="amount" class="form-label">{{ trans('cruds.payment_request.fields.amount') }}<span class="required">*</span></label>
            <input type="number" id="amount" name="amount" class="form-control" value="{{ isset($paymentRequest) && !empty($paymentRequest->amount) ? $paymentRequest->amount : '' }}" placeholder="{{ trans('cruds.payment_request.fields.amount') }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="due_date" class="form-label">{{ trans('cruds.payment_request.fields.due_date') }}<span class="required">*</span></label>
            <input type="text" id="due_date" name="due_date" class="form-control" value="{{ isset($paymentRequest) && !empty($paymentRequest->due_date) ? $paymentRequest->due_date : '' }}" placeholder="{{ trans('cruds.payment_request.fields.due_date') }}" readonly>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="society_id" class="form-label"> {{ trans('cruds.payment_request.fields.society') }}<span class="required">*</span></label>
            @if(!empty($user) && $user->is_sub_admin)
                <input type="hidden" name="society_id" class="form-control" value="{{ $user->society->uuid ?? '' }}">
                <input type="text" class="form-control"
                    value="{{ $user->society->name ?? '' }}"
                    placeholder="@lang('cruds.payment_request.fields.society')" disabled readonly>
            @else
            <select id="society_id" name="society_id" class="form-control h-auto society_id">
                <option value="">{{ trans('global.select') }} {{ trans('cruds.payment_request.fields.society') }}</option>
                @foreach ($societies as $key => $society)
                    <option value="{{ $key }}" {{ isset($paymentRequest) && !empty($paymentRequest->society_id) && $paymentRequest->society->uuid == $key ? 'selected' : '' }}>
                        {{ $society }}
                    </option>
                @endforeach
            @endif
            </select>
        </div>
    </div>

    <div class="col-lg-6">
    <div class="form-group">
        <label for="building_id" class="form-label"> 
            {{ trans('cruds.payment_request.fields.building') }}
            <span class="required">*</span>
        </label>
        

        <select id="building_id" name="building_id" class="form-control h-auto building_id">
            <option value="">{{ trans('global.select') }} {{ trans('cruds.payment_request.fields.building') }}</option>

            @if(!empty($user) && $user->is_sub_admin)
                @if(isset($buildings))
                    @foreach($buildings as $key => $building)
                        <option value="{{ $key }}" 
                            {{ isset($paymentRequest) && !empty($paymentRequest->building_id) && $paymentRequest->building->uuid == $key  ? 'selected' : '' }}>
                            {{ $building }}
                        </option>
                    @endforeach
                @endif
            @else
                @if(isset($paymentRequest))
                    @foreach ($buildings as $key => $building)
                        <option value="{{ $key }}" 
                            {{ isset($paymentRequest) && !empty($paymentRequest->building_id) && $paymentRequest->building_id == $key ? 'selected' : '' }}>
                            {{ $building }}
                        </option>
                    @endforeach
                @endif
            @endif
        </select>
    </div>
</div>


    <div class="col-lg-6">
        <div class="form-group">
            <label for="unit_id" class="form-label"> {{ trans('cruds.payment_request.fields.unit') }}<span class="required">*</span></label>
            <select id="unit_id" name="unit_id" class="form-control h-auto unit_id">
                @if(isset($paymentRequest))
                    <option value="">{{ trans('global.select') }} {{ trans('cruds.payment_request.fields.unit') }}</option>
                    @foreach ($units as $unitKey => $unit)
                        <option value="{{ $unitKey }}" {{ isset($paymentRequest) && !empty($paymentRequest->unit_id) && $paymentRequest->unit->uuid == $unitKey ? 'selected' : '' }}>
                            {{ $unit }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="pr_status" class="form-label"> {{ trans('cruds.payment_request.fields.status') }}<span class="required">*</span></label>
            <select id="pr_status" name="status" class="form-control h-auto">
                <option value="">{{ trans('global.select') }} {{ trans('cruds.payment_request.fields.status') }}</option>
                @foreach ($paymentRequestStatuses as $paymentRequstKey => $paymentRequestStatus)
                    <option value="{{ $paymentRequstKey }}" {{ isset($paymentRequest) && !empty($paymentRequest->status) && $paymentRequest->status == $paymentRequstKey ? 'selected' : '' }}>
                        {{ $paymentRequestStatus }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn"
                type="submit">{{ isset($paymentRequest) && !empty($paymentRequest) ? trans('global.update') : trans('global.save') }}
            </button>
            <a href="{{ route('admin.payment-requests.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>
