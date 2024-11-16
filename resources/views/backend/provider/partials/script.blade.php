<script>
    $(document).ready(function() {
        $(document).on("submit", "#providerAdd, #providerEdit", function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var verified = $("#is_verified").prop('checked');
            var featured = $("#is_featured").prop('checked');
            var formData = new FormData(this);
            formData.append('is_verified', verified ? 1 : 0);
            formData.append('is_featured', featured ? 1 : 0);
            storeUpdateProvider(formData, url)
        });
    });

    // update and store user
    function storeUpdateProvider(formData, url) {
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
                        window.location.href = "{{ route('admin.providers.index') }}";
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
                        // if (key === 'feature_ids') {
                        //     var selectElement = $("select[name='feature_ids[]']");
                        //     if (selectElement.length) {
                        //         selectElement.after(errorLabelTitle);
                        //     }
                        // } else {
                        if (key === 'society_id') {
                            var selectElement = $("select[name='society_id[]']");
                            if (selectElement.length) {
                                selectElement.after(errorLabelTitle);
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