<script>
    $(document).ready(function() {
        $(document).on("submit", "#complaintTypeAddForm, #complaintTypeEditForm", function(e) {
            e.preventDefault();
            var url = $(this).data('url');

            var verified = $("#mobile_verified").prop('checked');
            var formData = new FormData(this);
            formData.append('mobile_verified', verified ? 1 : 0);

            storeUpdateComplaintType(formData, url)
        });
    });

    // update and store user
    function storeUpdateComplaintType(formData, url) {
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
                        window.location.href = "{{ route('admin.users.index') }}";
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
                        if (key === 'roles') {
                            var selectElement = $("select[name='roles[]']");
                            if (selectElement.length) {
                                selectElement.after(errorLabelTitle);
                            }
                        } else if (key === 'password') {
                            var inputElement = $("input[name='" + key + "']");
                            if (inputElement.length) {
                                inputElement.parent().after(errorLabelTitle);
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
</script>