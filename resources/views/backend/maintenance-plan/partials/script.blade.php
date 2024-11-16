<script src="{{ asset('backend/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

<script>
    $(document).ready(function() {

        $("#year_of").datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true,
        });

        $(document).on("submit", "#maintenancePlanAddForm, #maintenancePlanEditForm", function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var formData = new FormData(this);
            storeUpdateMaintenancePlan(formData, url)
        });

        // let cloneIndex = $(".clone_columns").length;
        let cloneIndex = 1;

        $(document).on('click', '.add-option', function() {
            let clonedDiv = $(".clone_columns").first().clone();

            let count = $('.clone_columns').length;

            clonedDiv.find("input[type='checkbox']").prop('checked', false);
            clonedDiv.find("select").val('');

            // Update the id of the cloned textarea to be unique
            let newId = 'comments_' + cloneIndex;
            clonedDiv.find("input[type='text']").attr('id', newId).val('');


            let newbudgetId = 'budget_' + cloneIndex;
            clonedDiv.find("input[type='number']").attr('id', newbudgetId).val('');


            clonedDiv.find('select[name="maintenance_item_id"]').attr('name', 'maintenance_item_id[' + cloneIndex + ']');

            // Ensure checkboxes for month are set as an array with the current index
            clonedDiv.find('input[type="checkbox"]').each(function(index) {
                $(this).attr('name', 'month[' + cloneIndex + '][' + index + ']'); // Add index to checkbox names
            });

            // Change the button to remove in the cloned div
            clonedDiv.find('.AddOptionBtn2').html('<button type="button" class="btn btn-danger remove-option"><i class="ri-delete-bin-line"></i></button>');


            clonedDiv.find('input[type="checkbox"]').attr('name', `item[${count}][month][]`);
            clonedDiv.find('input[type="text"]').attr('name', `item[${count}][comments][]`);
            clonedDiv.find('select').attr('name', `item[${count}][maintenance_item_id][]`);
            clonedDiv.find('input[type="number"]').attr('name', `item[${count}][budget][]`);

            clonedDiv.appendTo("#clone-showing-data");

            cloneIndex++;
        });


        $(document).on('click', '.remove-option', function() {
            $(this).closest('.clone_columns').remove();
        });
    });

    // update and store maintenance plan
    function storeUpdateMaintenancePlan(formData, url) {
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
                        window.location.href = "{{ route('admin.maintenance-plans.index') }}";
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

                    // Remove existing error messages
                    $('.validation-error-block').remove();

                    $.each(errors, function(key, item) {
                        var errorLabelTitle = '<span class="validation-error-block text-danger">' + item[0] + '</span>';

                        // Check if the error key is for an 'item' array field
                        if (key.includes('item')) {
                            // Extract index and field name from the error key (e.g., 'item.0.comments.0')
                            var keyParts = key.split('.'); // ['item', '0', 'comments', '0']
                            var index = keyParts[1]; // Index of the item
                            var field = keyParts[2]; // Field name ('comments', 'maintenance_item_id', 'month')

                            // Target specific elements based on the field name
                            if (field === 'maintenance_item_id') {
                                console.log(field);

                                var maintenanceItemField = $("select[name='item[" + index + "][maintenance_item_id][]']");
                                var maintenanceErrorMessage = '<span class="validation-error-block text-danger">Item field is required.</span>';

                                if (maintenanceItemField.length) {
                                    maintenanceItemField.after(maintenanceErrorMessage);
                                }

                            } else if (field === 'budget') {
                                console.log(field);
                                var budgetField = $("input[name='item[" + index + "][budget][]']");
                                var budgetErrorMessage = '<span class="validation-error-block text-danger">Budget field is required.</span>';

                                if (budgetField.length) {
                                    budgetField.after(budgetErrorMessage);
                                }

                            } else if (field === 'comments') {
                                console.log(field);

                                var commentsField = $("input[name='item[" + index + "][comments][]']");
                                var commentErrorMessage = '<span class="validation-error-block text-danger">Comment field is required.</span>';

                                if (commentsField.length) {
                                    commentsField.after(commentErrorMessage);
                                }

                            } else if (field === 'month') {
                                console.log(field);

                                var monthField = $("input[type='checkbox'][name='item[" + index + "][month][]']");
                                var monthErrorMessage = '<span class="validation-error-block text-danger">Month field is required.</span>';

                                // Correcting the append method here
                                if (monthField.length) {
                                    monthField.closest('div').after(monthErrorMessage);
                                }
                            }
                        } else {
                            console.log('else');

                            // Handle non-nested errors
                            var inputElement = $("input[name='" + key + "'], textarea[name='" + key + "'], select[name='" + key + "']");
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