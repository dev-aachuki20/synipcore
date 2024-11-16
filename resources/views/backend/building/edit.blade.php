@extends('layouts.admin')
@section('title', trans('cruds.building.title_singular'))
@section('custom_css')

@endsection

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">@lang('global.edit') @lang('global.new') @lang('cruds.building.title_singular')</h4>
                </div>
                <div class="card-body">
                    <form class="msg-form" id="buildingEdit" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <input type="hidden" name="id" value="{{$building->uuid}}">
                        @include('backend.building._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_js')
    <script>
        @can('building_edit')
            $('#buildingEdit').on('submit', function(event) {
                event.preventDefault();

                var formData = new FormData(this);

                $('.error').remove();
                $('.loader-div').show();
                $(".submitBtn").attr('disabled', true);
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.buildings.update', $building->uuid) }}",
                    data: formData,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $(".submitBtn").attr('disabled', false);
                        $('.loader-div').hide();
                        if(response.success){
                            toasterAlert('success', response.message);
                            setTimeout(() => {
                                window.location.href="{{route('admin.buildings.index')}}" ;
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
        @endcan
    </script>
@endsection
