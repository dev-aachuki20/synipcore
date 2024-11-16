@extends('layouts.admin')
@section('title', trans('cruds.menus.roles'))

@section('custom_css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
@endsection

@section('main-content')

    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">@lang('cruds.role.title')</h4>
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

    <script>
        $(document).ready(function(e) {
            $(document).on('datatableLoaded', function() {
                var buttonCount = $('.dt-paging-button').not('.previous, .next').length;
                if (buttonCount <= 1) {
                    $('.paging_simple_numbers').addClass('d-none');
                }
            })
        })

        @can('role_delete')
            $(document).on("click", ".deleteRoleBtn", function() {
                var url = $(this).data('href');
                Swal.fire({
                        title: "{{ trans('global.areYouSure') }}",
                        text: "{{ trans('global.onceClickedRecordDeleted') }}",
                        icon: "warning",
                        showDenyButton: true,
                        confirmButtonText: "{{ trans('global.swl_confirm_button_text') }}",
                        denyButtonText: "{{ trans('global.swl_deny_button_text') }}",
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
                                        $('#role-table').DataTable().ajax.reload(null, false);
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
