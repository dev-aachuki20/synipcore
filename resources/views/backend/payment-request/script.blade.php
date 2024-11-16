<script>
    $(document).ready(function(e){
        $("#due_date").flatpickr({altInput:!0, minDate: 'today', altFormat:"{{config('constant.date_format.date')}}",dateFormat:"Y-m-d"})
    })

    $(document).on('change', '.society_id', function() {
        var societyUuid = $(this).val();        
        getSocietyBuildingUnits(societyUuid, 'building');
    });

    // get units by building id
    $(document).on('change', '.building_id', function() {
        var buildingUuid = $(this).val();
        getSocietyBuildingUnits(buildingUuid, 'unit');
    });
</script>