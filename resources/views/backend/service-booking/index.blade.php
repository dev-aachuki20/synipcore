@extends('layouts.admin')
@section('title', trans('cruds.menus.service_bookings'))

@section('custom_css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
@endsection

@section('main-content')

<div class="row">
    <div class="col-12">
        <div class="card mt-20">
            <div class="card-header">
                <h4 class="mb-0">@lang('cruds.service_booking.title')</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive common-table nth_child2_table">
                    {{ $dataTable->table(['class' => 'table mb-0', 'style' => 'width:100%;']) }}

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('custom_js')
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    {!! $dataTable->scripts() !!}
    <script type="text/javascript">
        /* Status Update  */
        $(document).on("change", ".statusUpdate", function(e) {
            e.preventDefault();
            var selectedValue = $(this).val();
            var selectedOption = $(this).find('option[value="' + selectedValue + '"]');
            var serviceId = selectedOption.data('service_booking_id');
            var serviceStatus = selectedOption.data('service_booking_status');

            Swal.fire({
                title: "{{ trans('global.areYouSure') }}",
                text: "{{ trans('global.want_to_change_status') }}",
                icon: "warning",
                showDenyButton: true,
                confirmButtonText: "{{ trans('global.swl_confirm_button_text') }}",
                denyButtonText: "{{ trans('global.swl_deny_button_text') }}",
                allowOutsideClick: false,
            }).then(function(result) {
                if (result.isConfirmed) {
                    $('.loader-div').show();
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('admin.statusChange') }}",
                        dataType: 'json',
                        data: {
                            _token: "{{ csrf_token() }}", 
                            id: serviceId,
                            status: serviceStatus,
                        },
                        success: function(response) {
                            $('.loader-div').hide();
                            if (response.success) {
                                $('#servicebooking-table').DataTable().ajax.reload(null, false);
                                toasterAlert('success', response.message);
                            } else {
                                toasterAlert('error', response.message);
                            }
                        },
                        error: function(res) {
                            $('.loader-div').hide();
                            toasterAlert('error', res.responseJSON.error);
                        },
                        complete: function() {
                            $('.loader-div').hide();
                        }
                    });
                }
            });
        });


        @can('service_booking_delete')
            $(document).on("click", ".btnDeleteServiceBooking", function(e) {
                e.preventDefault();
                var url = $(this).data('href');
                Swal.fire({
                        title: "{{ trans('global.areYouSure') }}",
                        text: "{{ trans('global.onceClickedRecordDeleted') }}",
                        icon: "warning",
                        showDenyButton: true,
                        confirmButtonText: "{{ trans('global.swl_confirm_button_text') }}",
                        denyButtonText: "{{ trans('global.swl_deny_button_text') }}",
                        allowOutsideClick: false,
                    })
                    .then(function(result) {
                        if (result.isConfirmed) {
                            $('.loader-div').show();
                            $.ajax({
                                type: 'DELETE',
                                url: url,
                                dataType: 'json',
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(response) {
                                    if (response.success) {
                                        $('#servicebooking-table').DataTable().ajax.reload(null, false);
                                        toasterAlert('success', response.message);
                                    } else {
                                        toasterAlert('error', response.error);
                                    }
                                },
                                error: function(res) {
                                    toasterAlert('error', res.responseJSON.error);
                                },
                                complete: function(xhr) {
                                    $('.loader-div').hide();
                                }
                            });
                        }
                    });
            });
        @endcan
    </script>
@endsection
