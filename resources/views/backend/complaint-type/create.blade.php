@extends('layouts.admin')
@section('title', trans('cruds.complaint_type.title_singular'))
@section('custom_css')
    <link href="{{ asset('backend/vendor/dropify/dropify.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">{{ trans('global.add') }} {{ trans('global.new') }}
                        {{ trans('cruds.complaint_type.title_singular') }}</h4>
                </div>
                <div class="card-body">
                    <form class="msg-form" id="complaintTypeAddForm" enctype="multipart/form-data">
                        @csrf
                        @include('backend.complaint-type._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_js')
    <script>
        function validateMinValue(input) {
            var errorContainer = document.getElementById('sort_order_error');
            if (input.value < 1) {
                errorContainer.innerHTML = "{{ trans('messages.minimum_value') }}";
            } else {
                errorContainer.innerHTML = '';
            }
        }
        
        let debounceTimeout;

        function createSlug(titleElement) {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(function() {
                const title = $(titleElement).val().trim();
                if (!title) return;
                $("#error-message").remove();
                $.ajax({
                    type: "GET",
                    url: "{{ route('admin.createComplaintSlug') }}",
                    data: {
                        title: title
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('.loader-div').hide();
                        $("#error-message").remove();
                        if (response.success) {
                            if (response.data_type == 'not_exist') {
                                $("#slug").val(response.data);
                            }
                        } else {
                            if (response.data_type == 'already_exist') {
                                $("#slug").val(response.data);
                                $(".form-group").css('margin-bottom', '0px');
                                $('<div id="error-message" class="error-message text-danger">' +
                                    "{{ trans('messages.service.slug') }}" + '</div>').insertAfter(
                                    '.input-slug');
                            }
                        }
                    },
                    error: function() {
                        $('.loader-div').hide();
                        if (response.responseJSON.error_type == 'something_error') {
                            toasterAlert('error', response.responseJSON.error);
                        } else {
                            $(".form-group").css('margin-bottom', '0px');
                            $('<div id="error-message" class="error-message text-danger">' +
                                "{{ trans('messages.error_message') }}" + '</div>').insertAfter(
                                '.input-slug');
                        }
                    }
                });
            }, 300);
        }
      

        @can('complaint_type_create')
            $(document).on('submit', '#complaintTypeAddForm', function(e) {
                e.preventDefault();
                $('.loader-div').show();
                $(".submitBtn").attr('disabled', true);
                $('.validation-error-block').remove();

                var formData = new FormData(this);

                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('admin.complaint-types.store') }}",
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toasterAlert('success', response.message);
                            $('#complaintTypeAddForm')[0].reset();

                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('admin.complaint-types.index') }}";
                            }, 1000);

                            if (response.image) {
                                $('.user-profile-img').attr('src', response.image);
                            }
                        }
                    },
                    error: function(response) {
                        $(".loader-div").css('display', 'none');
                        $(".submitBtn").attr('disabled', false);
                        if (response.responseJSON.error_type == 'something_error') {
                            toasterAlert('error', response.responseJSON.error);
                        } else {
                            var errors = response.responseJSON.errors;
                            $.each(errors, function(key, item) {
                                var errorLabelTitle = '<span class="validation-error-block">' +
                                    item[0] + '</span>';
                                var inputElement = $("input[name='" + key + "']");
                                if (inputElement.length) {
                                    inputElement.after(errorLabelTitle);
                                }
                            });
                        }
                    },
                    complete: function(res) {
                        $(".submitBtn").attr('disabled', false);
                        $('.loader-div').hide();
                    }
                });
            });
        @endcan
    </script>
@endsection
