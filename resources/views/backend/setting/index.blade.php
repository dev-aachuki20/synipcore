@extends('layouts.admin')
@section('title', trans('cruds.setting.title_singular'))
@section('custom_css')
<!-- dropify css -->
<link href="{{ asset('backend/vendor/dropify/dropify.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('main-content')
<!-- <div class="page-title-box">
        <h4 class="page-title">@lang('cruds.setting.title')</h4>
    </div> -->
<div class="row">
    <div class="col-12">
        <div class="card mt-20">
            <div class="card-header">
                <h4 class="mb-0">@lang('cruds.setting.title')</h4>
            </div>
            <div class="card-body">
                <form class="msg-form demo settingPageTab" id="settingform" enctype="multipart/form-data">
                    <ul class="nav nav-pills nav-justified gap-0" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="#admin" data-bs-toggle="tab" aria-expanded="false" class="nav-link rounded-0 active" aria-selected="true" role="tab">
                                {{ trans('global.admin') }}
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#application" data-bs-toggle="tab" aria-expanded="true" class="nav-link rounded-0" aria-selected="false" role="tab" tabindex="-1">
                                {{ trans('global.application') }}
                            </a>
                        </li>
                    </ul>
                    @csrf
                    <div class="tab-content">
                        <div class="tab-pane active show" id="admin" role="tabpanel">
                            <div class="row">
                                @foreach($settings as $key => $setting)

                                @if($setting->setting_type == 'text' && $setting->key == 'site_title')
                                <div class="col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label class="form-label">{{ $setting->display_name}} <span class="required">*</span></label>
                                        <input type="text" class="form-control" value="{{$setting->value}}" name="{{$setting->key}}" required />
                                    </div>
                                </div>
                                @endif

                                @if($setting->setting_type == 'image')
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label">{{ $setting->display_name}} </label>
                                        <input name="{{$setting->key}}" type="file" class="dropify" id="image-input-{{$setting->key}}" data-default-file=" {{ $setting->image_url ? $setting->image_url : asset(config('constant.default.logo')) }}" data-show-loader="true" data-errors-position="outside" data-allowed-file-extensions="jpeg png jpg PNG JPG" accept="image/jpeg, image/png, image/jpg, image/PNG, image/JPG" />
                                    </div>
                                </div>
                                @endif

                                @if($setting->setting_type == 'file')
                                @php
                                if($setting->key == 'terms_condition'){
                                $docUrl = $setting->doc_url ? $setting->doc_url : asset(config('constant.default.terms_condition_pdf'));
                                } else if($setting->key == 'privacy_policy'){
                                $docUrl = $setting->doc_url ? $setting->doc_url : asset(config('constant.default.privacy_pdf'));
                                }else if($setting->key == 'about_us'){
                                $docUrl = $setting->doc_url ? $setting->doc_url : asset(config('constant.default.privacy_pdf'));
                                }
                                @endphp
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label w-100 d-flex align-items-center justify-content-between mt-2"> {{ $setting->display_name}}
                                            <a href="{{ $docUrl }}" title="Preview {{ $setting->display_name}} PDF" target="_blank" class="btn btn-dark btn-sm preview_btn"> <i class="ri-eye-fill"></i> </a>
                                        </label>
                                        <input name="{{$setting->key}}" type="file" class="dropify" id="image-input-{{$setting->key}}" data-default-file="{{ $docUrl }}" data-show-loader="true" data-errors-position="outside" data-allowed-file-extensions="pdf" accept=".pdf" />
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="tab-pane" id="application" role="tabpanel">
                            <div class="row">
                                @foreach($settings as $key => $setting)
                                @if($setting->setting_type == 'text' && $setting->key !== 'site_title')
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label">{{ $setting->display_name}} <span class="required">*</span></label>
                                        <input type="text" class="form-control" value="{{$setting->value}}" name="{{$setting->key}}" required />
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="bottombtn-group">
                        <button type="submit" class="btn btn-success submitBtn">@lang('global.update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


@section('custom_js')
<!-- dropify Js -->
<script src="{{ asset('backend/vendor/dropify/dropify.min.js') }}"></script>
<script>
    $(document).ready(function() {

        $('.dropify').dropify();
        $('.dropify-errors-container').remove();
        $('.dropify-wrapper').find('.dropify-clear').hide();

        $(document).on('submit', '#settingform', function(e) {
            e.preventDefault();
            $('.loader-div').show();
            $(".submitBtn").attr('disabled', true);

            $('.validation-error-block').remove();

            var formData = new FormData(this);

            $.ajax({
                type: 'post',
                url: "{{ route('admin.update.setting') }}",
                dataType: 'json',
                contentType: false,
                processData: false,
                data: formData,
                success: function(response) {
                    if (response.success) {
                        toasterAlert('success', response.message);
                        window.location.reload();
                    }
                },
                error: function(response) {
                    console.log(response);
                    if (response.responseJSON.error_type == 'something_error') {
                        toasterAlert('error', response.responseJSON.error);
                    } else {
                        var errorLabelTitle = '';
                        $.each(response.responseJSON.errors, function(key, item) {
                            errorLabelTitle = '<span class="validation-error-block">' + item[0] + '</sapn>';

                            $(errorLabelTitle).insertAfter("input[name='" + key + "']");

                            /* if(key == 'profile_image'){
                                $(errorLabelTitle).insertAfter("#"+key);
                            } */
                        });
                    }
                },
                complete: function(res) {
                    $(".submitBtn").attr('disabled', false);
                    $('.loader-div').hide();
                }
            });
        });
    });
</script>
@endsection