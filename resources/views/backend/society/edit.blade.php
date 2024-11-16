@extends('layouts.admin')
@section('title', trans('cruds.society.title_singular'))
@section('custom_css')

@endsection

@section('main-content')
<div class="row">
    <div class="col-12">
        <div class="card mt-20">
            <div class="card-header">
                <h4 class="mb-0">@lang('global.edit') @lang('global.new') @lang('cruds.society.title_singular')</h4>
            </div>
            <div class="card-body">
                <form class="msg-form" id="societyEdit" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <input type="hidden" name="id" value="{{ $society->uuid }}">
                    @include('backend.society._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('custom_js')
<script>
    @can('society_edit')
    $('#societyEdit').on('submit', function(event) {
        event.preventDefault();

        var fire_alert = $("#fire_alert").prop('checked');
        var lift_alert = $("#lift_alert").prop('checked');
        var animal_alert = $("#animal_alert").prop('checked');
        var visitor_alert = $("#visitor_alert").prop('checked');

        var formData = new FormData(this);
        formData.append('fire_alert', fire_alert ? 1 : 0);
        formData.append('lift_alert', lift_alert ? 1 : 0);
        formData.append('animal_alert', animal_alert ? 1 : 0);
        formData.append('visitor_alert', visitor_alert ? 1 : 0);

        $('.error').remove();
        $('.loader-div').show();
        $(".submitBtn").attr('disabled', true);
        $.ajax({
            type: "POST",
            url: "{{ route('admin.societies.update', $society->uuid) }}",
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                $(".submitBtn").attr('disabled', false);
                $('.loader-div').hide();
                if (response.success) {
                    toasterAlert('success', response.message);
                    // $('#societyEdit')[0].reset();
                    setTimeout(() => {
                        window.location.href = "{{ route('admin.societies.index') }}";
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

                        var errorHtml = '<div class="error text-danger">' + errors[fieldName][
                            0
                        ] + '</div>';
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
    @endcan


    $(document).ready(function() {
        $('#city').on('change', function() {
            var cityId = $(this).val();

            if (cityId === '') {
                $('#district').html('<option value="">Select District</option>');
                return;
            }

            $.ajax({
                url: "{{route('admin.getDistricts')}}",
                type: 'GET',
                data: {
                    city_id: cityId
                },
                success: function(response) {
                    if (response.districts !== null) {
                        $('#district').html('<option value="">Select District</option>');

                        $.each(response.districts, function(id, title) {
                            $('#district').append('<option value="' + id + '">' + title.charAt(0).toUpperCase() + title.slice(1) + '</option>');
                        });

                        let selectedDistrict = "{{ $society->district ?? '' }}";
                        if (selectedDistrict) {
                            $('#district').val(selectedDistrict);
                        }
                    } else {
                        $('#district').html('<option value="">No districts available</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching districts:', error);
                }
            });

        });

        // Trigger change event on page load if city is already selected (for edit mode)
        if ($('#city').val() !== '') {
            $('#city').trigger('change');
        }
    });
</script>
@endsection