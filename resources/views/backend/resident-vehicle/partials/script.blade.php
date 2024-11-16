<script>
    $(document).ready(function() {
        $(document).on("submit", "#vehicleAddForm, #vehicleEditForm", function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var formData = new FormData(this);
            storeUpdateVehicle(formData, url)
        });
    });

    // update and store post
    function storeUpdateVehicle(formData, url) {
        $('.validation-error-block').remove();
        $('.loader-div').show();
        $(".submitBtn").attr('disabled', true);
        $.ajax({
            type: 'post',
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
                        window.location.href = "{{ route('admin.resident-vehicles.index') }}";
                    }, 1000);
                } else {
                    toasterAlert('error', response.message);
                }
            },
            error: function(response) {
                $('.loader-div').hide();
                $(".submitBtn").attr('disabled', false);
                if (response.responseJSON.error_type == 'something_error') {
                    toasterAlert('error', response.responseJSON.error);
                } else {
                    var errors = response.responseJSON.errors;
                    $.each(errors, function(key, item) {
                        var errorLabelTitle =
                            '<span class="validation-error-block">' + item[0] +
                            '</span>';
                        if (key === 'status') {
                            var selectElement = $("select[name='status']");
                            if (selectElement.length) {
                                selectElement.after(errorLabelTitle);
                            }
                        } else if (key === 'resident_id') {
                            var selectElement = $("select[name='resident_id']");
                            if (selectElement.length) {
                                selectElement.after(errorLabelTitle);
                            }
                        } else if (key === 'society_id') {
                            var selectElement = $("select[name='society_id']");
                            if (selectElement.length) {
                                selectElement.after(errorLabelTitle);
                            }
                        } else if (key === 'building_id') {
                            var selectElement = $("select[name='building_id']");
                            if (selectElement.length) {
                                selectElement.after(errorLabelTitle);
                            }
                        } else if (key === 'unit_id') {
                            var selectElement = $("select[name='unit_id']");
                            if (selectElement.length) {
                                selectElement.after(errorLabelTitle);
                            }
                        } else {
                            var inputElement = $("input[name='" + key + "']");
                            if (inputElement.length) {
                                inputElement.after(errorLabelTitle);
                            }
                        }
                    });
                }
            },
            complete: function() {
                $('.loader-div').hide();
                $(".submitBtn").attr('disabled', false);
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
            url: "{{ route('admin.filterLocation') }}",
            data: {
                type: 'get_building_by_society',
                id: societyId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $(".building_id").html('');
                    $(".building_id").append(response.data);
                } else {}
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
            url: "{{ route('admin.filterLocation') }}",
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