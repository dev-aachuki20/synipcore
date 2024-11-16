@extends('layouts.admin')
@section('title', trans('cruds.menus.posts'))

@section('custom_css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
@endsection

@section('main-content')

    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">{{ isset($post) ? ucfirst($post->title) : '' }}</h4>
                    <p class="m-0 fw-600">{{ trans('cruds.post.fields.total_comments') }} <span
                            class="total-comment">{{ $post->comments->count() ?? 0 }}</span></p>
                </div>
                <div class="card-body">
                    <ul class="post-comment-list p-0 m-0">
                        @forelse ($post->comments as $record)
                            <li>
                                <div class="comment-bottom">
                                    <div class="comment-left">
                                        <span class="comment-user-img">
                                            <img src="{{ isset($record->user->profile_image_url) && $record->user->profile_image_url != '' ? $record->user->profile_image_url : asset('default/user-icon.svg') }}"
                                                alt="user-image" class="img-fluid">
                                        </span>
                                        <h4>{{ $record->user->name ?? '' }}</h4>
                                    </div>
                                    <div class="comment-right">
                                        <ul>
                                            <li><span class="approve-comment">
                                                    @if ($record->is_approve == 1)
                                                        {{ config('constant.status_type.comment_status')[$record->is_approve] }}
                                                    @else
                                                        <div class="checkbox switch comments_switch">
                                                            <label>
                                                                <input type="checkbox"
                                                                    class="switch-control comment_status_cb"
                                                                    {{ $record->is_approve == 1 ? 'checked' : '' }}
                                                                    data-comment_id="{{ $record->id }}" />
                                                                <span class="switch-label"></span>
                                                            </label>
                                                        </div>
                                                    @endif
                                                </span></li>
                                            <li><span class="comment-time">{{ $record->created_at->format('h:iA') }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="comment-top">
                                    <p>{{ $record->comment }}</p>
                                </div>
                            </li>
                        @empty
                            <li>
                                <div class="comment-top">
                                    <p>{{ trans('global.no_comment_found') }}</p>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('custom_js')

    <script type="text/javascript">
        @can('comment_approve')
            $(document).on('click', '.comment_status_cb', function() {
                var $this = $(this);
                var commentId = $this.data('comment_id');
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
                                url: "{{ route('admin.comments.status') }}",
                                dataType: 'json',
                                data: {
                                    _token: csrf_token,
                                    id: commentId
                                },
                                success: function(response) {
                                    if (response.status == 'true') {
                                        window.location.reload();
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
        @endcan
    </script>
@endsection
