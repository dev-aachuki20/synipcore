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
                    <h4 class="mb-0">{{ trans('global.add') }} {{ trans('global.new') }}
                        {{ trans('cruds.payment_request.title_singular') }}</h4>
                </div>
                <div class="card-body">
                    <form class="msg-form" id="paymentRequestAddForm">
                        @csrf
                        @include('backend.payment-request._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_js')
    <script src="{{asset('backend/vendor/flatpickr/flatpickr.min.js')}}"></script>

    @include('backend.payment-request.script')
    <script>        
        @can('payment_request_create')
            $(document).on('submit', '#paymentRequestAddForm', function(e) {
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
                    url: "{{ route('admin.payment-requests.store') }}",
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toasterAlert('success', response.message);
                            $('#paymentRequestAddForm')[0].reset();
                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('admin.payment-requests.index') }}";
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
                        $('.loader-div').hide();
                    }
                });
            });
        @endcan
    </script>
@endsection
