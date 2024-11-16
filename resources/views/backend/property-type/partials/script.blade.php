<script>
    $(document).ready(function() {
        $(document).on("submit", "#propertyTypeAddForm, #propertyTypeEditForm", function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var formData = new FormData(this);
            storeUpdatePropertyType(formData, url)
        });
    });

    // update and store feature
    function storeUpdatePropertyType(formData, url) {
        console.log();
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
                        window.location.href = "{{ route('admin.prpoertyTypes.index') }}";
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
                        console.log('item', item);
                        var errorLabelTitle = '<span class="validation-error-block">' +
                            item[0] + '</span>';
                        var inputElement = $("input[name='" + key + "']");
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