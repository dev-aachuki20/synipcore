@extends('layouts.admin')
@section('title', trans('cruds.menus.user'))

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
                    <h4 class="mb-0">@lang('cruds.user.title')</h4>
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
    <script>
        var selectRolePlaceholder = "{{ trans('global.select_role') }}"
        var selectSocietyPlaceholder = "{{ trans('global.select_society') }}"
    </script>
    {!! $dataTable->scripts() !!}

    <script>
        $(document).ready(function(e) {
            $(document).on('datatableLoaded', function() {
                var buttonCount = $('.dt-paging-button').not('.previous, .next').length;
                if (buttonCount <= 1) {
                    $('.paging_simple_numbers').addClass('d-none');
                }
            });
        })

        @can('user_view')
            $(document).on("click", ".btnViewUser", function() {
                $('.loader-div').show();
                var url = $(this).data('href');

                $.ajax({
                    type: 'get',
                    url: url,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('.popup_render_div').html(response.htmlView);
                            $('#ViewUser').modal('show');
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
            });
        @endcan

        @can('user_delete')
            $(document).on("click", ".deleteUserBtn", function() {
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
                                        $('#user-table').DataTable().ajax.reload(null, false);
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

        $(document).on('click', '.user_status_cb', function() {
            var $this = $(this);
            var userId = $this.data('user_id');
            var flag = true;
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            if ($this.prop('checked')) {
                flag = false;
            }
            Swal.fire({
                    title: "{{ trans('global.areYouSure') }}",
                    text: "{{ trans('global.want_to_change_status') }}",
                    icon: "warning",
                    showDenyButton: true,
                    confirmButtonText: "{{ trans('global.swl_confirm_button_text') }}",
                    denyButtonText: "{{ trans('global.swl_deny_button_text') }}",
                })
                .then(function(result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: "{{ route('admin.user.status') }}",
                            dataType: 'json',
                            data: {
                                _token: csrf_token,
                                id: userId
                            },
                            success: function(response) {
                                if (response.status == 'true') {
                                    toasterAlert('success', response.message);
                                    $('#user-table').DataTable().ajax.reload(null, false);
                                }
                            },
                            error: function(response) {
                                $this.prop('checked', flag);
                                toasterAlert('error', response.error);
                            }
                        });
                    } else {
                        $this.prop('checked', flag);
                    }
                });
        });
    </script>

@endsection