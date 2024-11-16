@extends('layouts.admin')
@section('title', trans('cruds.service.title_singular'))
@section('custom_css')
    <link href="{{ asset('backend/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/vendor/dropify/dropify.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">@lang('global.add') @lang('global.new') @lang('cruds.service.title_singular')</h4>
                </div>
                <div class="card-body">
                    <form class="msg-form" id="serviceAdd" enctype="multipart/form-data">
                        @csrf
                        @include('backend.service._form')
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
                errorContainer.innerHTML = "{{ trans('messages.minimum_value')}}";
            } else {
                errorContainer.innerHTML = '';
            }
        }

        /* Start Create A Slug */
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
                    url: "{{ route('admin.createServiceSlug') }}",
                    data: { 
                        title: title 
                    },
                    dataType: 'json',
                    success: function(response) {
                        // $('.loader-div').hide();
                        $("#error-message").remove();
                        if (response.success) {
                            if(response.data_type == 'not_exist'){
                                $("#slug").val(response.data);
                            }
                        } else {
                            if(response.data_type == 'already_exist'){
                                $("#slug").val(response.data);
                                $(".form-group").css('margin-bottom', '0px');
                                $('<div id="error-message" class="error-message text-danger">' + "{{ trans('messages.service.slug') }}" + '</div>').insertAfter('.input-slug');
                            }
                        }
                    },
                    error: function() {
                        // $('.loader-div').hide();
                        if(response.responseJSON.error_type == 'something_error'){
                            toasterAlert('error',response.responseJSON.error);
                        } else{
                            $(".form-group").css('margin-bottom', '0px');
                            $('<div id="error-message" class="error-message text-danger">' + "{{ trans('messages.error_message') }}" + '</div>').insertAfter('.input-slug');
                        }
                    }
                });
            }, 300);
        }
        /* End Slug */
    </script>

    <script>
        $('#serviceAdd').on('submit', function(event) {
            event.preventDefault();
            var featured = $("#is_featured").prop('checked');
            var formData = new FormData(this);
            formData.append('is_featured', featured ? 1 : 0);

            $("#error-message").remove();
            $('.error').remove();
            
            $('.loader-div').show();
            $(".submitBtn").attr('disabled', true);
            $.ajax({
                type: "POST",
                url: "{{ route('admin.services.store') }}",
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function(response) {
                    $(".submitBtn").attr('disabled', false);
                    $('.loader-div').hide();
                    if(response.success){
                        toasterAlert('success', response.message);
                        $('#serviceAdd')[0].reset();
                        setTimeout(() => {
                            window.location.href="{{route('admin.services.index')}}" ;
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

                            var errorHtml = '<div class="error text-danger">' + errors[fieldName][0] + '</div>';
                            var fieldSelector = '[name="' + fieldName + '"]';
                            
                            if ($(fieldSelector).length) {
                                $(fieldSelector).after(errorHtml);
                            }
                        }
                    }
                },
                complete: function(res){
                    $('.loader-div').hide();
                    $(".submitBtn").attr('disabled', false);
                }                        
            });
        });
    </script>
@endsection

