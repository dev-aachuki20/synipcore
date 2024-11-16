@extends('layouts.admin')
@section('title', trans('cruds.building.title_singular'))

@section('custom_css')

@endsection

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">@lang('global.add') @lang('global.new') @lang('cruds.building.title_singular')</h4>
                </div>
                <div class="card-body">
                    <form class="msg-form" id="buildingAdd" enctype="multipart/form-data">
                        @csrf
                        @include('backend.building._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_js')
    <script>
        $('#buildingAdd').on('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(this);

            $('.loader-div').show();
            $('.error').remove();
            $(".submitBtn").attr('disabled', true);
            $.ajax({
                type: "POST",
                url: "{{ route('admin.buildings.store') }}",
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function(response) {
                    $('.loader-div').hide();
                    $(".submitBtn").attr('disabled', false);
                    if(response.success){
                        toasterAlert('success', response.message);
                        $('#buildingAdd')[0].reset();
                        setTimeout(() => {
                            window.location.href="{{route('admin.buildings.index')}}" ;
                        }, 1000);
                    } else {
                        toasterAlert('error', response.message);
                    }
                },
                error: function(response) {
                    $('.loader-div').hide();
                    if(response.responseJSON.error_type == 'something_error'){
                        toasterAlert('error',response.responseJSON.error);
                    } else {                    
                        var errors = response.responseJSON.errors;
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

