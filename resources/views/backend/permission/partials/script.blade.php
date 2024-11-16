<script>
    $(document).ready(function() {
        $(document).on("submit", "#permissionForm, #permissionEditForm", function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var formData = new FormData(this);

            storeUpdatePermission(formData, url)
        });
    });

    // update and store user
    function storeUpdatePermission(formData, url) {
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
                        window.location.href = "{{ route('admin.permissions.index') }}";
                    }, 1000);
                } else {
                    toasterAlert('error', response.message);
                }
            },
            error: function(response) {
                if (response.responseJSON.error_type == 'something_error') {
                    toasterAlert('error', response.responseJSON.error);
                } else {
                    var errors = response.responseJSON.errors;
                    $.each(errors, function(key, item) {
                        var errorLabelTitle =
                            '<span class="validation-error-block">' +
                            item[0] + '</span>';
                        var selectElement = $("input[name='" + key + "']");
                        if (selectElement.length) {
                            selectElement.after(errorLabelTitle);
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