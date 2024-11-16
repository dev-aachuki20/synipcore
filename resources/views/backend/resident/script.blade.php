<script>
    $(document).on('click', '.toggle-password', function() {
        var passwordInput = $(this).prev('input');
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            $(this).removeClass('show-password');
        } else {
            passwordInput.attr('type', 'password');
            $(this).addClass('show-password');
        }
    });

    $(document).on('change', '.society_id', function() {
        var selectedValue = $(this).val();
        var selectedOption = $(this).find('option[value="' + selectedValue + '"]');
        var societyId = selectedOption.data('society_id');

        if (!societyId) {
            $(".unit_id").html('');
            $(".building_id").html('');
            return;
        }

        $(".unit_id").html('');
        $(".building_id").html('');
        // $('.loader-div').show();
        $.ajax({
            type: "GET",
            url: "{{ route('admin.filterResident') }}",
            data: {
                type: 'get_building_by_society',
                id: societyId
            },
            dataType: 'json',
            success: function(response) {
                // $('.loader-div').hide();
                if (response.success) {
                    $(".building_id").html('');
                    $(".building_id").append(response.data);
                } else {

                }
            },
            error: function(response) {
                // $('.loader-div').hide();
                toasterAlert('error', response.responseJSON.error);
            }
        });
    });

    $(document).on('change', '.building_id', function() {
        var selectedValue = $(this).val();
        var selectedOption = $(this).find('option[value="' + selectedValue + '"]');
        var buildingId = selectedOption.data('building_id');

        if (!buildingId) {
            $(".unit_id").html('');
            return;
        }

        $(".unit_id").html('');
        // $('.loader-div').show();
        $.ajax({
            type: "GET",
            url: "{{ route('admin.filterResident') }}",
            data: {
                type: 'get_unit_by_building',
                id: buildingId
            },
            dataType: 'json',
            success: function(response) {
                // $('.loader-div').hide();
                if (response.success) {
                    $(".unit_id").html('');
                    $(".unit_id").append(response.data);
                } else {

                }
            },
            error: function(response) {
                // $('.loader-div').hide();
                toasterAlert('error', response.responseJSON.error);
            }
        });
    });
</script>