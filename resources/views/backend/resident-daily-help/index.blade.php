@extends('layouts.admin')
@section('title', trans('cruds.menus.resident_daily_helps'))

@section('custom_css')
    <link href="{{ asset('backend/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('main-content')

    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">@lang('cruds.resident_daily_help.title')</h4>
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
<script src="{{ asset('backend/vendor/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('backend/vendor/daterangepicker/daterangepicker.js') }}"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>

    {!! $dataTable->scripts() !!}
    <script type="text/javascript">
        @can('resident_daily_help_delete')
            $(document).on("click", ".btnDeleteDailyHelp", function(e) {
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
                                        $('#resident-daily-help-table').DataTable().ajax.reload(
                                            null,
                                            false);
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
