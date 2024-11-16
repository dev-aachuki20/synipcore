<script>
    $(document).ready(function() {

        $(document).on("submit", "#ResidentDailyHelpAdd, #ResidentDailyHelpEdit", function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var formData = new FormData(this);
            storeUpdateResidentDailyHelp(formData, url)
        });
    });

    $(document).on('change', '.society_id', function() {
        var societyUuid = $(this).val();
        getSocietyBuildingUnits(societyUuid, 'building');
    });

    // get units by building id
    $(document).on('change', '.building_id', function() {
        var buildingUuid = $(this).val();
        getSocietyBuildingUnits(buildingUuid, 'unit');
    });

    // update and store user
    function storeUpdateResidentDailyHelp(formData, url) {
        $('.loader-div').show();
        $(".submitBtn").attr('disabled', true);
        $('.validation-error-block').remove();
        $.ajax({
            type: 'post',
            // headers: {
            //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            // },
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
                        window.location.href = "{{ route('admin.resident-daily-helps.index') }}";
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

                        var inputElement = $("input[name='" + key +
                            "'], select[name='" + key + "']");
                        if (inputElement.length) {
                            inputElement.after(errorLabelTitle);
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
</script>