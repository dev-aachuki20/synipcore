<script>
    $(document).ready(function() {
        $(document).on("submit", "#visitorForm", function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var formData = new FormData(this);
            storeUpdateVisitor(formData, url)
        });
    });

    // update and store visitors
    function storeUpdateVisitor(formData, url) {
        $('.loader-div').show();
        $(".submitBtn").attr('disabled', true);
        $('.validation-error-block').remove();
        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            dataType: 'json',
            contentType: false,
            processData: false,
            data: formData,
            success: function(response) {
                $(".submitBtn").attr('disabled', false);
                $('.loader-div').hide();
                if (response.success) {
                    toasterAlert('success', response.message);
                    setTimeout(() => {
                        window.location.href = "{{ route('admin.visitors.index') }}";
                    }, 1000);
                } else {
                    toasterAlert('error', response.message);
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
                        var errorLabelTitle = '<span class="validation-error-block">' +
                            item[0] + '</span>';
                        if (key === 'phone_number') {
                            var inputElement = $("input[name='" + key + "']");
                            if (inputElement.length) {
                                inputElement.closest('.number_prefix').after(errorLabelTitle);
                            }
                        } else if (key === 'society_id') {
                            var selectElement = $("select[name='society_id']");
                            if (selectElement.length) {
                                selectElement.closest('.form-group').append(errorLabelTitle);
                            }
                        } else if (key === 'cab_number') {
                            var selectElement = $("input[name='cab_number']");
                            if (selectElement.length) {
                                selectElement.closest('.form-group').after(errorLabelTitle);
                            }
                        } else if (key === 'keep_package') {
                            var selectElement = $("input[name='keep_package']");
                            if (selectElement.length) {
                                selectElement.closest('.form-group').after(errorLabelTitle);
                            }
                        } else {
                            var inputElement = $("input[name='" + key +
                                "'], select[name='" + key + "']");
                            if (inputElement.length) {
                                inputElement.after(errorLabelTitle);
                            }
                        }
                    });
                }
            },
            complete: function() {
                $(".submitBtn").attr('disabled', false);
                $('.loader-div').hide();
            }
        });
    }


    $(document).on('change', '.society_id', function() {
        var selectedValue = $(this).val();
        var selectedOption = $(this).find('option[value="' + selectedValue + '"]');
        var societyId = selectedOption.data('society_id');

        if (!societyId) {
            $(".unit_id").html('');
            $(".building_id").html('');
            return;
        }

        $(".unit_id").html('');
        $(".building_id").html('');
        $.ajax({
            type: "GET",
            url: "{{ route('admin.filterResident') }}",
            data: {
                type: 'get_building_by_society',
                id: societyId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $(".building_id").html('');
                    $(".building_id").append(response.data);
                } else {

                }
            },
            error: function(response) {
                toasterAlert('error', response.responseJSON.error);
            }
        });
    });

    $(document).on('change', '.building_id', function() {
        var selectedValue = $(this).val();
        var selectedOption = $(this).find('option[value="' + selectedValue + '"]');
        var buildingId = selectedOption.data('building_id');
        if (!buildingId) {
            $(".unit_id").html('');
            return;
        }
        $(".unit_id").html('');
        $.ajax({
            type: "GET",
            url: "{{ route('admin.filterResident') }}",
            data: {
                type: 'get_unit_by_building',
                id: buildingId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $(".unit_id").html('');
                    $(".unit_id").append(response.data);
                } else {

                }
            },
            error: function(response) {
                toasterAlert('error', response.responseJSON.error);
            }
        });
    });
</script>