<div class="row">
    <input type="hidden" name="announcement_managementIds" id="announcement_managementIds">

    <div class="col-lg-6">
        <div class="form-group">
            <label for="type" class="form-label"> {{ trans('cruds.announcement.fields.type') }}<span
                    class="required">*</span></label>
            <select id="type" name="announcement_type" class="form-control h-auto announcementType">
                <option value="">{{ trans('global.select') }}</option>
                @foreach (config('constant.annuncement_types') as $key => $val)
                <option value="{{ $key }}"
                    {{ isset($announcement) && !empty($announcement->announcement_type) && $announcement->announcement_type == $key ? 'selected' : '' }}>
                    {{ $val }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="title" class="form-label">{{ trans('cruds.announcement.fields.title') }}<span
                    class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-control"
                value="{{ isset($announcement) && !empty($announcement->title) ? $announcement->title : '' }}"
                placeholder="{{ trans('cruds.announcement.fields.title') }}">
        </div>
    </div>
    <div class="pollType d-none">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="expire_date" class="form-label">{{ trans('cruds.announcement.fields.expire_date') }}<span class="required">*</span></label>
                    <input type="text" id="expire_date" name="expire_date" class="form-control"
                        placeholder="{{ trans('cruds.announcement.fields.expire_date') }}"
                        value="{{ isset($announcement) && !empty($announcement->expire_date) ? $announcement->expire_date->format('Y-m-d h:i A') : '' }}">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="poll_type" class="form-label">{{ trans('cruds.announcement.fields.poll_type') }}<span
                            class="required">*</span></label>
                    <select id="poll_type" name="poll_type" class="form-control h-auto">
                        <option value="">{{ trans('global.select') }}</option>
                        @foreach (config('constant.poll_type') as $key => $val)
                        <option value="{{ $key }}"
                            {{ isset($announcement) && !empty($announcement->poll_type) && $announcement->poll_type == $key ? 'selected' : '' }}>
                            {{ $val }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row defaultOptRow">
            @if(isset($announcement) && !empty($announcement->options))
            @foreach($announcement->options as $index => $option)
            <div class="col-lg-6 option-row">
                <div class="form-group">
                    <label for="options" class="form-label">{{ trans('cruds.announcement.fields.option') }} {{ $index + 1 }}<span
                            class="required">*</span></label>
                    <input type="text" name="options[]" class="form-control options"
                        value="{{ $option->option }}"
                        id="options_{{ $index + 1 }}">
                    @if($index > 1)
                    <button type="button" class="btn btn-danger remove-option">-</button>
                    @endif

                    @if($index == 1)
                    <div class="AddOptionBtn">
                        <button type="button" class="btn btn-success add-option">+</button>
                    </div>
                    @endif

                </div>
            </div>
            @endforeach
            @else
            <div class="col-lg-6 option-row">
                <div class="form-group">
                    <label for="options" class="form-label">{{ trans('cruds.announcement.fields.option') }} 1 <span
                            class="required">*</span></label>
                    <input type="text" name="options[]" class="form-control options" id="options_1">
                </div>
            </div>
            <div class="col-lg-6 option-row">
                <div class="form-group">
                    <label for="options" class="form-label">{{ trans('cruds.announcement.fields.option') }} 2 <span
                            class="required">*</span></label>
                    <input type="text" name="options[]" class="form-control options" id="options_2">
                    <div class="AddOptionBtn">
                        <button type="button" class="btn btn-success add-option">+</button>
                    </div>
                </div>
            </div>
            @endif
        </div>

    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="posted_by" class="form-label">{{ trans('cruds.announcement.fields.posted_by') }}<span
                    class="required">*</span></label>
            <input disabled type="text" id="posted_by" name="posted_by" class="form-control" value="{{auth()->user()->name}}"
                placeholder="{{ trans('cruds.announcement.fields.posted_by') }}">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="address" class="form-label">{{ trans('cruds.announcement.fields.society') }}<span
                    class="required">*</span></label>
            @if(!empty($user) && $user->is_sub_admin)
            <input type="hidden" name="society_id" class="form-control" value="{{ $user->society->id ?? '' }}">
            <input type="text" class="form-control"
                value="{{ $user->society->name ?? '' }}"
                placeholder="@lang('cruds.building.fields.society')" disabled readonly>
            @else
            <select id="society" name="society_id" class="form-control h-auto">
                <option value="">{{ trans('global.select') }}</option>
                @forelse($society as $key=>$val)
                <option value="{{ $val->id }}"
                    {{ isset($announcement) && !empty($announcement->society_id) && $announcement->society_id == $val->id ? 'Selected' : '' }}>
                    {{ $val->name }}
                </option>
                @empty
                <option value="">{{ trans('not_found') }}</option>
                @endforelse
            </select>
            @endif
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label for="announcement_image" class="form-label">{{ trans('global.image') }}</label>
            <div id="announcement_image" class="form-control dropzone">
                <div class="dz-default dz-message">{{ trans('global.drag_drop') }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <label for="message" class="form-label">{{ trans('cruds.announcement.fields.message') }}<span
                    class="required">*</span></label>
            <textarea class="form-control" id="message" name="message" cols="30" rows="5"
                placeholder="{{ trans('cruds.announcement.fields.message') }}">{{ isset($announcement) && !empty($announcement->message) ? $announcement->message : '' }}</textarea>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn"
                type="submit">{{ isset($announcement) && !empty($announcement) ? trans('global.update') : trans('global.save') }}
            </button>
            <a href="{{ route('admin.announcements.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>