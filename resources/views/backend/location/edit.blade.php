@extends('layouts.admin')
@section('title', trans('cruds.location.title_singular'))
@section('custom_css')

@endsection

@section('main-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">@lang('global.add') @lang('global.new') @lang('cruds.location.title_singular')</h4>
                </div>
                <div class="card-body">
                    <form class="msg-form" id="locationEdit" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <input type="hidden" value="{{ $location->uuid }}" name="id">
                        @include('backend.location.form')
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
                errorContainer.innerHTML = 'The value must be at least 1.';
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
                // $('.loader-div').show();
                $.ajax({
                    type: "GET",
                    url: "{{ route('admin.createLocationSlug') }}",
                    data: {
                        title: title
                    },
                    dataType: 'json',
                    success: function(response) {
                        // $('.loader-div').hide();
                        $("#error-message").remove();
                        if (response.success) {
                            $("#slug").val(response.data);
                        } else {
                            if (response.data_type == 'already_exist') {
                                $("#slug").val(response.data);
                                $('<div id="error-message" class="error-message text-danger">' +
                                    "{{ trans('messages.location.slug') }}" + '</div>').insertAfter(
                                    '#slug');
                            }
                        }
                    },
                    error: function(response) {
                        // $('.loader-div').hide();
                        if (response.responseJSON.error_type == 'something_error') {
                            toasterAlert('error', response.responseJSON.error);
                        } else {
                            $('<div id="error-message" class="error-message text-danger">' +
                                "{{ trans('messages.error_message') }}" + '</div>').insertAfter(
                                '#slug');
                        }
                    }
                });
            }, 300);
        }
        
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $('#locationEdit').on('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(this);

            $("#error-message").remove();
            $('.error').remove();
            $('.loader-div').show();
            $(".submitBtn").attr('disabled', true);
            $.ajax({
                type: "POST",
                url: "{{ route('admin.locations.update', $location->uuid) }}",
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    $(".submitBtn").attr('disabled', false);
                    $('.loader-div').hide();
                    if (response.success) {
                        toasterAlert('success', response.message);
                        // $('#locationEdit')[0].reset();
                        setTimeout(() => {
                            window.location.href = "{{ route('admin.locations.index') }}";
                        }, 1000);
                    } else {
                        toasterAlert('error', response.message);
                    }
                },
                error: function(xhr) {
                    $('.loader-div').hide();
                    var errors = xhr.responseJSON.errors;

                    for (const fieldName in errors) {
                        if (errors.hasOwnProperty(fieldName)) {
                            console.log(fieldName);

                            var errorHtml = '<div class="error text-danger">' + errors[fieldName][0] +
                                '</div>';
                            var fieldSelector = '[name="' + fieldName + '"]';

                            if ($(fieldSelector).length) {
                                $(fieldSelector).after(errorHtml);
                            }
                        }
                    }
                },
                complete: function(res) {
                    $('.loader-div').hide();
                    $(".submitBtn").attr('disabled', false);
                }
            });
        });
    </script>
@endsection
