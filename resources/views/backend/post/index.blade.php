@extends('layouts.admin')
@section('title', trans('cruds.menus.posts'))

@section('custom_css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

@endsection

@section('main-content')

    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">@lang('cruds.post.title')</h4>
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
        @can('post_delete')
            $(document).on("click", ".btnDeletePost", function(e) {
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
                                        $('#post-table').DataTable().ajax.reload(null, false);
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

        @can('post_view')
            $(document).on("click", ".btnViewPost", function() {
                $('.loader-div').show();
                var url = $(this).data('href');

                $.ajax({
                    type: 'get',
                    url: url,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('.popup_render_div').html(response.htmlView);
                            $('#ViewPost').modal('show');
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


        /* Status Update  */
        $(document).on("change", ".statusUpdate", function(e) {
            e.preventDefault();
            var selectedValue = $(this).val();
            var selectedOption = $(this).find('option[value="' + selectedValue + '"]');
            var postId = selectedOption.data('post_id');
            var postStatus = selectedOption.data('post_status');

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
                        url: "{{ route('admin.postStatusChange') }}",
                        dataType: 'json',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: postId,
                            status: postStatus,
                        },
                        success: function(response) {
                            $('.loader-div').hide();
                            if (response.success) {
                                $('#post-table').DataTable().ajax.reload(null, false);
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
    </script>
@endsection