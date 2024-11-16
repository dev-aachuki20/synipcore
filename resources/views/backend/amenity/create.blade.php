@extends('layouts.admin')
@section('title', trans('cruds.amenity.title_singular'))

@section('custom_css')

@endsection

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">{{ trans('global.add') }} {{ trans('global.new') }}
                        {{ trans('cruds.amenity.title_singular') }}</h4>
                </div>
                <div class="card-body">
                    <form class="msg-form" id="amenityAddForm">
                        @csrf
                        @include('backend.amenity._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_js')

    <script>
        @can('amenity_create')
            $(document).on('submit', '#amenityAddForm', function(e) {
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
                    url: "{{ route('admin.amenities.store') }}",
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toasterAlert('success', response.message);
                            $('#amenityAddForm')[0].reset();
                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('admin.amenities.index') }}";
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
                               
                                var inputElement = $("input[name='" + key +"'], select[name='" + key + "']");
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
