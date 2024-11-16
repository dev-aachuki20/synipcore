@extends('layouts.admin')
@section('title', trans('cruds.aibox_notification.title_singular'))

@section('custom_css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css">

@endsection

@section('main-content')
<div class="row">
    <div class="col-12">
        <div class="card mt-20">
            <div class="card-header">
                <h4 class="mb-0">@lang('cruds.aibox_notification.title')</h4>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>

{!! $dataTable->scripts() !!}

<script>
    @can('aibox_notification_view')
    $(document).on("click", ".btnViewAiboxDetails", function() {
        $('.loader-div').show();
        var url = $(this).data('href');

        $.ajax({
            type: 'get',
            url: url,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('.popup_render_div').html(response.htmlView);
                    $('#ViewAiboxDetails').modal('show');
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

    @can('aibox_notification_view_image')
    $(document).ready(function() {
        $(document).on("click", ".btnViewAiboxImageDetails", function(e) {
            e.preventDefault();
            $('.loader-div').show();

            var url = $(this).data('href');
            var id = $(this).data('id');
            $.ajax({
                type: 'post',
                url: url,
                data: {
                    _token: "{{ csrf_token() }}",
                    'id': id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('.popup_render_div .ai_images a').magnificPopup({
                            type: 'image',
                            closeBtnInside: true,
                            gallery: {
                                enabled: true
                            }
                        });

                        $('.popup_render_div .ai_images a').first().trigger('click');
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
    });
    @endcan

    @can('aibox_notification_view_video')
    $(document).on("click", ".btnViewAiboxVideoDetails", function() {
        $('.loader-div').show();
        var url = $(this).data('href');
        var id = $(this).data('id');

        $.ajax({
            type: 'post',
            url: url,
            data: {
                _token: "{{ csrf_token() }}",
                'id': id
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('.popup_render_div').html(response.htmlView);
                    $('.popup_render_div .ai_videos a').magnificPopup({
                        type: 'iframe',
                        closeBtnInside: true,
                        gallery: {
                            enabled: true
                        }
                    });

                    $('.popup_render_div .ai_videos a').first().trigger('click');
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
</script>
@endsection