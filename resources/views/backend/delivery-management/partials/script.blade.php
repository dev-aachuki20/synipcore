<script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>

<script>
    $(document).ready(function() {
        CKEDITOR.replace('message', {
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
            resize_enabled: false,
        });

        $(document).on("submit", "#deliveryManagementAddForm, #deliveryManagementEditForm", function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var formData = new FormData(this);
            storeUpdateDeliveryManagement(formData, url)
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

    // Display society, building and unit on the basis of selected delivery type
    $(document).on('change', '.delivery_type_id', function() {
        var delivery_type_id = $(this).val();

        if (delivery_type_id) {
            $.ajax({
                url: "{{route('admin.getNotifyUserRole')}}",
                type: 'GET',
                data: {
                    delivery_type_id: delivery_type_id,
                },
                success: function(response) {
                    if (response.success) {
                        var notifyUserRole = response.notifyUserRole;

                        if (notifyUserRole == 'guard' || notifyUserRole == 'resident' || notifyUserRole == 'admin') {
                            $('.society-field').show();
                        } else {
                            $('.society-field').hide();
                        }

                        if (notifyUserRole == 'guard' || notifyUserRole == 'resident' || notifyUserRole == 'admin') {
                            $('.building-field').show();
                        } else {
                            $('.building-field').hide();
                        }

                        if (notifyUserRole == 'resident') {
                            $('.unit-field').show();
                        } else {
                            $('.unit-field').hide();
                        }


                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
    });


    // update and store user
    function storeUpdateDeliveryManagement(formData, url) {
        $('.loader-div').show();
        $(".submitBtn").attr('disabled', true);
        $('.validation-error-block').remove();
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
                        window.location.href = "{{ route('admin.delivery-managements.index') }}";
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
                        if (key === 'message') {
                            var selectElement = $("textarea[name='message']");
                            if (selectElement.length) {
                                selectElement.parent().append(errorLabelTitle);
                            }
                        } else if (key === 'society_id') {
                            var selectElement = $("select[name='society_id']");
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