<script>
    // $(document).on('click', "#addMetaFields", function (e) {
    //     e.preventDefault();

    //     var newRowHtml = '<div class="row cloneMetaFieldsRow">' +
    //         '<div class="col-md-6">' +
    //             '<div class="form-group">' +
    //                 '<label class="form-label">Key </label>' +
    //                 '<input type="text" name="key[]" class="form-control" required>' +
    //             '</div>' +
    //         '</div>' +
    //         '<div class="col-md-5">' +
    //             '<div class="form-group">' +
    //                 '<label class="form-label">Value </label>' +
    //                 '<input type="text" id="value" name="value[]" class="form-control" required>'+
    //             '</div>' +
    //         '</div>' +
    //         '<div class="col-md-1">' +
    //             '<div class="form-group">' +
    //                 '<i class="ri-close-line removeMetaField" style="font-size:2rem;"></i>' +
    //             '</div>' +
    //         '</div>' +
    //     '</div>';
    //     $('#metaFormContainerContainer').append(newRowHtml);
    // });

    // $(document).on('click', '.removeMetaField', function() {
    //     Swal.fire({
    //         title: 'Are you sure?',
    //         text: 'Do you want to delete this item?',
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: 'Okay',
    //         allowOutsideClick: false,
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             $(this).closest('.cloneMetaFieldsRow').remove();
    //         }
    //     });
    // });

    /* Filter Data */
    $(document).on('change', '.society_id', function() {
        var selectedValue = $(this).val();
        var selectedOption = $(this).find('option[value="' + selectedValue + '"]');
        var societyId = selectedOption.data('society_id');

        if (!societyId) {
            $(".building_id").html('');
            return;
        }

        $(".building_id").html('');
        $.ajax({
            type: "GET",
            url: "{{ route('admin.filterResident') }}",
            data: {
                type: 'get_building_by_society',
                id: societyId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $(".building_id").html('');
                    $(".building_id").append(response.data);
                } else {

                }
            },
            error: function(response) {
                toasterAlert('error', response.responseJSON.error);
            }
        });
    });
</script>