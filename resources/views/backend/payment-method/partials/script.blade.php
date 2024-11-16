<script>
    let debounceTimeout;

    function createSlug(titleElement) {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(function() {
            const title = $(titleElement).val().trim();
            if (!title) return;
            $("#error-message").remove();
            $.ajax({
                type: "GET",
                url: "{{ route('admin.createPaymentMethodSlug') }}",
                data: {
                    title: title
                },
                dataType: 'json',
                success: function(response) {
                    $("#error-message").remove();
                    if (response.success) {
                        $("#slug").val(response.data);
                    } else {
                        if (response.data_type == 'already_exist') {
                            $("#slug").val(response.data);
                            $('<div id="error-message" class="error-message text-danger">' +
                                "{{ trans('messages.post.slug') }}" + '</div>').insertAfter(
                                '#slug');
                        }
                    }
                },
                error: function(response) {
                    if (response.responseJSON.error_type == 'something_error') {
                        toasterAlert('error', response.responseJSON.error);
                    } else {
                        $('<div id="error-message" class="error-message text-danger">' +
                            "{{ trans('messages.error_message') }}" + '</div>').insertAfter(
                            '#slug');
                    }
                }
            });
        }, 300);
    }

    $(document).ready(function() {
        $(document).on("submit", "#PaymentMethodAddForm, #paymentMethodEditForm", function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var formData = new FormData(this);
            storeUpdateProject(formData, url)
        });
    });

    // update and store post
    function storeUpdateProject(formData, url) {
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
                        // window.location.reload();
                        window.location.href = "{{ route('admin.payment-methods.index') }}";
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
                        } else {
                            var inputElement = $("input[name='" + key +
                                "'], textarea[name='" + key + "'], select[name='" + key + "']");
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
</script>