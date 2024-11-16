<script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>

<script>
    $(document).ready(function() {
        CKEDITOR.replace('description', {
            toolbar: [{
                    name: 'basicstyles',
                    items: ['Bold', 'Italic', 'Underline']
                },
                {
                    name: 'paragraph',
                    items: ['NumberedList', 'BulletedList']
                },
                {
                    name: 'links',
                    items: ['Link', 'Unlink']
                },
                {
                    name: 'styles',
                    items: ['Font', 'FontSize']
                }
            ],
            removePlugins: 'elementspath',
            resize_enabled: false
        });

        $(document).on("submit", "#categoryAddForm, #categoryEditForm", function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var formData = new FormData(this);
            storeUpdateCategory(formData, url)
        });
    });

    // update and store user
    function storeUpdateCategory(formData, url) {
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
                        window.location.href = "{{ route('admin.categories.index') }}";
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
                        if (key === 'description') {
                            var selectElement = $("textarea[name='description']");
                            if (selectElement.length) {
                                selectElement.parent().append(errorLabelTitle);
                            }
                        } else {
                            var inputElement = $("input[name='" + key +
                                "'], textarea[name='" + key + "']");
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
