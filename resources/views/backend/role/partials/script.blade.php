<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllButton = document.getElementById('select-all');
        const deselectAllButton = document.getElementById('deselect-all');
        const permissionsSelect = $('#permissions');

        selectAllButton.addEventListener('click', function() {
            permissionsSelect.find('option').prop('selected', true).trigger('change');
        });

        deselectAllButton.addEventListener('click', function() {
            permissionsSelect.find('option').prop('selected', false).trigger('change');
        });

        // Initialize the select2 plugin if not already initialized
        if (!permissionsSelect.hasClass('select2-hidden-accessible')) {
            permissionsSelect.select2();
        }
    });
    $(document).ready(function() {
        $(document).on("submit", "#roleForm, #roleEditForm", function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var formData = new FormData(this);
            storeUpdateRole(formData, url)
        });
    });

    // update and store user
    function storeUpdateRole(formData, url) {
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
                        window.location.href = "{{ route('admin.roles.index') }}";
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
                    setTimeout(function() {
                        window.location.href =
                            "{{ route('admin.users.index') }}";
                    }, 1000);
                } else {
                    var errors = response.responseJSON.errors;
                    $.each(errors, function(key, item) {
                        var errorLabelTitle =
                            '<span class="validation-error-block">' +
                            item[0] + '</span>';
                        if (key === 'permissions') {
                            $("#permissions").next('.select2-container')
                                .after(errorLabelTitle);
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