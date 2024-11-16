<div class="row">
    <div class="col-lg-4">
        <div class="form-group">
            <label for="society_id" class="form-label">@lang('cruds.maintenance_plan.fields.society')<span class="required">*</span></label>
            @if(!empty($user) && $user->is_sub_admin)
            <input type="hidden" name="society_id" class="form-control" value="{{ $user->society->id ?? '' }}">
            <input type="text" class="form-control" value="{{ $user->society->name ?? '' }}" placeholder="@lang('cruds.guard.fields.society')" disabled readonly>
            @else
            <select id="society_id" name="society_id" class="form-control h-auto">
                <option value="">{{ trans('global.select') }}</option>
                @foreach ($societies as $key => $society)
                <option
                    value="{{ $society->id }}" {{ isset($maintenancePlan) && $maintenancePlan->society_id == $society->id ? 'selected' : '' }}>
                    {{ $society->name }}
                </option>
                @endforeach
            </select>
            @endif
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label for="society_id" class="form-label">@lang('cruds.maintenance_plan.fields.year')<span class="required">*</span></label>
            <input type="text" id="year_of" name="year_of" class="form-control"
                value="{{ isset($maintenancePlan) && !empty($maintenancePlan->year_of) ? $maintenancePlan->year_of : '' }}"
                placeholder="XXXX">
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label for="total_budget" class="form-label">{{ trans('cruds.maintenance_plan.fields.total_budget') }}<span
                    class="required">*</span></label>
            <input type="number" id="total_budget" name="total_budget" class="form-control"
                value="{{ isset($maintenancePlan) && !empty($maintenancePlan->total_budget) ? $maintenancePlan->total_budget : '' }}"
                placeholder="{{ trans('cruds.maintenance_plan.fields.total_budget') }}">
        </div>
    </div>

    @if(isset($maintenancePlanItems) && $maintenancePlanItems->count())
    @foreach($maintenancePlanItems as $index => $mainItem)
    <div class="col-lg-12 clone_columns">
        <div class="row">
            @if($index == 0)
            <div class="col-xl-12 add_options_row text-end">
                <div class="form-group position-relative mb-0">
                    <div class="AddOptionBtn2" id="AddOptionBtn2">
                        <button type="button" class="btn btn-success add-option" id="add-option"><i class="ri-add-line"></i></button>
                    </div>
                </div>
            </div>
            @else
            <div class="col-xl-12 add_options_row text-end">
                <div class="form-group position-relative mb-0">
                    <div class="AddOptionBtn2" id="AddOptionBtn2">
                        <button type="button" class="btn btn-danger remove-option"><i class="ri-delete-bin-line"></i></button>
                    </div>
                </div>
            </div>
            @endif

            <input type="hidden" name="item[{{ $index }}][id]" class="form-control h-auto" value="{{$mainItem->id
            }}">
            <div class="col-xl-2">
                <div class="form-group">
                    <label for="maintenance_item_id_{{ $index }}" class="form-label">
                        @lang('cruds.maintenance_plan.fields.item')<span class="required">*</span>
                    </label>
                    <select id="maintenance_item_id_{{ $index }}" name="item[{{ $index }}][maintenance_item_id][]" class="form-control h-auto maintenance_item">
                        <option value="">{{ trans('global.select') }}</option>
                        @foreach ($maintenanceItem as $item)
                        <option
                            data-type="{{ $item->duration }}"
                            value="{{ $item->id }}"
                            {{ $mainItem->maintenance_item_id == $item->id ? 'selected' : '' }}>
                            {{ $item->title }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="form-group month_main">
                    <label for="months_{{ $index }}" class="form-label">Month</label>
                    <div class="whole_months">
                        @foreach(config('constant.plan_months') as $month)
                        <label>
                            <input class="maintenance_month" type="checkbox" name="item[{{ $index }}][month][]" value="{{ $month }}"
                                {{ in_array($month, json_decode($mainItem->month, true)) ? 'checked' : '' }}>
                            <span>{{ $month }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-xl-2 pe-xl-0">
                <div class="form-group">
                    <label class="form-label">
                        @lang('cruds.maintenance_plan.fields.budget')
                    </label>
                    <input type="number" name="item[{{$index}}][budget][]" class="form-control maintenance_budget" value="{{ $mainItem->budget ?? '' }}" placeholder=" {{ trans('cruds.maintenance_plan.fields.budget') }}">
                </div>
            </div>
            <div class="col-xl-2">
                <div class="form-group">
                    <label for="comments_{{ $index }}" class="form-label">
                        {{ trans('cruds.maintenance_plan.fields.comments') }}
                    </label>
                    <input type="text"
                        name="item[{{ $index }}][comments][]"
                        id="comments_{{ $index }}"
                        class="form-control maintenance_comment" value="{{ $mainItem->comments ?? '' }}" placeholder=" {{ trans('cruds.maintenance_plan.fields.comments') }}">
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @else
    <div class="col-lg-12 clone_columns">
        <div class="row">
            <div class="col-xl-12 add_options_row text-end">
                <div class="form-group position-relative mb-0">
                    <div class="AddOptionBtn2" id="AddOptionBtn2">
                        <button type="button" class="btn btn-success add-option" id="add-option"><i class="ri-add-line"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-xl-2">
                <div class="form-group">
                    <label class="form-label">
                        @lang('cruds.maintenance_plan.fields.item')<span class="required">*</span>
                    </label>
                    <select id="maintenance_item_id_0" name="item[0][maintenance_item_id][]" class="form-control h-auto maintenance_item">
                        <option value="">{{ trans('global.select') }}</option>
                        @foreach ($maintenanceItem as $item)
                        <option data-type="{{ $item->duration }}" value="{{ $item->id }}">
                            {{ $item->title }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-xl-6 px-xl-0">
                <div class="form-group month_main">
                    <label for="months_0" class="form-label">@lang('cruds.maintenance_plan.fields.month')<span class="required">*</span></label>
                    <div class="whole_months">
                        @foreach(config('constant.plan_months') as $month)
                        <label>
                            <input type="checkbox" name="item[0][month][]" value="{{ $month }}" class="maintenance_month">
                            <span>{{ $month }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-xl-2 pe-xl-0">
                <div class="form-group">
                    <label class="form-label">
                        @lang('cruds.maintenance_plan.fields.budget')
                    </label>
                    <input type="number" name="item[0][budget][]" id="budget_0" class="form-control maintenance_budget" placeholder=" {{ trans('cruds.maintenance_plan.fields.budget') }}">
                </div>
            </div>
            <div class="col-xl-2">
                <div class="form-group">
                    <label class="form-label">
                        @lang('cruds.maintenance_plan.fields.comments')
                    </label>
                    <input type="text" name="item[0][comments][]" id="comments_0" class="form-control maintenance_comment" placeholder=" {{ trans('cruds.maintenance_plan.fields.comments') }}">

                </div>
            </div>
        </div>
    </div>
    @endif


    <div id="clone-showing-data"></div>
    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn"
                type="submit">{{ isset($maintenancePlan) && !empty($maintenancePlan) ? trans('global.update') : trans('global.save') }}
            </button>
            <a href="{{ route('admin.maintenance-plans.index') }}"
                class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>