@extends('layouts.admin')
@section('title', trans('cruds.resident.title_singular'))
@section('custom_css')

@endsection

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">@lang('global.edit') @lang('global.new') @lang('cruds.resident.title_singular')</h4>
                </div>
                <div class="card-body">
                    <form class="msg-form" id="residentEdit" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <input type="hidden" name="id" value="{{$resident->uuid}}">
                        @include('backend.resident._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_js')
    @include('backend.resident.script')
    <script>
        @can('resident_edit')
            $('#residentEdit').on('submit', function(event) {
                event.preventDefault();

                $('.error').remove();
                var verified = $("#is_verified").prop('checked');
                var formData = new FormData(this);
                if(verified){
                    formData.append('is_verified', 1);
                }else{
                    formData.append('is_verified', 0);
                }
                
                $('.loader-div').show();
                $(".submitBtn").attr('disabled', true);
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.residents.update', $resident->uuid) }}",
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
                                window.location.href="{{route('admin.residents.index')}}" ;
                            }, 1000);
                            // $('#residentEdit')[0].reset();
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
