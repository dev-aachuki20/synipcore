@extends('layouts.admin')
@section('title', trans('cruds.unit.title_singular'))
@section('custom_css')
    
@endsection

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">@lang('global.add') @lang('global.new') @lang('cruds.unit.title_singular')</h4>
                </div>
                <div class="card-body">
                    <form class="msg-form" id="unitAdd">
                        @csrf
                        @include('backend.unit._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_js')
    @include('backend.unit.script')
    <script>
        $('#unitAdd').on('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(this);

            $('.error').remove();
            $('.loader-div').show();
            $(".submitBtn").attr('disabled', true);
            $.ajax({
                type: "POST",
                url: "{{ route('admin.units.store') }}",
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function(response) {
                    $(".submitBtn").attr('disabled', false);
                    $('.loader-div').hide();
                    if(response.success){
                        toasterAlert('success', response.message);
                        $('#unitAdd')[0].reset();
                        setTimeout(() => {
                            window.location.href="{{route('admin.units.index')}}" ;
                        }, 1000);
                    } else {
                        toasterAlert('error', response.message);
                    }
                },
                error: function(response) {
                    console.log(response)
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

