@extends('layouts.admin')
@section('title', trans('cruds.payment_request.title_singular'))
@section('custom_css')
<link rel="stylesheet" href="{{ asset('backend/vendor/flatpickr/flatpickr.min.css')}}">
@endsection

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">@lang('global.edit') @lang('global.new') @lang('cruds.payment_request.title_singular')</h4>
                </div>
                <div class="card-body">
                    <form class="msg-form" id="paymentRequestEditForm">
                        @csrf
                        @method('PUT')
                        @include('backend.payment-request._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_js')
    @include('backend.payment-request.script')
    <script src="{{asset('backend/vendor/flatpickr/flatpickr.min.js')}}"></script>
    <script>
        @can('payment_request_edit')
            $(document).on('submit', '#paymentRequestEditForm', function(e) {
                e.preventDefault();

                $(".submitBtn").attr('disabled', true);
                $('.validation-error-block').remove();
                $(".loader-div").css('display', 'block');

                var formData = new FormData(this);
                var url = "{{ route('admin.payment-requests.update', $paymentRequest->uuid) }}";


                $.ajax({
                    type: 'POST',
                    url: url,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        $(".loader-div").css('display', 'none');

                        $(".submitBtn").attr('disabled', false);
                        if (response.success) {
                            toasterAlert('success', response.message);
                            setTimeout(function() {
                                window.location.href = "{{ route('admin.payment-requests.index') }}";
                            }, 1000);
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
                                var errorLabelTitle = '<span class="validation-error-block">' +item[0] + '</span>';
                                
                                if(key == 'due_date'){
                                    $('#due_date').next('input[type="text"]').after(errorLabelTitle);
                                } else {
                                    var inputElement = $("input[name='" + key +"'], select[name='" + key + "']");
                                    if (inputElement.length) {
                                        inputElement.after(errorLabelTitle);
                                    }
                                }
                            });
                        }
                    },
                    complete: function(res) {
                        $(".submitBtn").attr('disabled', false);
                    }
                });
            });
        @endcan
    </script>
@endsection
