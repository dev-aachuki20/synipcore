<div class="modal fade show" id="ViewComment" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-modal="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">{{ trans('global.show') }}
                    {{ trans('cruds.comment.title_singular') }}
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="normal_width_table">
                        <table class="table table-striped mb-0">
                            <tbody>
                                <tr>
                                    <th style="width:150px;"> {{ trans('cruds.comment.fields.type') }} </th>
                                    <td>
                                        <div class="show-fulldescription"> {{ class_basename($comment->commentable_type) ?? 'N/A' }} </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width:150px;"> {{ trans('cruds.comment.fields.comment') }} </th>
                                    <td>
                                        <div class="show-fulldescription"> {{ $comment->comment ?? 'N/A' }} </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.comment.fields.user') }} </th>
                                    <td> {{ $comment->user->name ?? 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.comment.fields.status') }} </th>
                                    <td>
                                        @php
                                        $commentStatus = config('constant.status_type.comment_status');
                                        @endphp
                                        {{ $comment->is_approve == 1 ? $commentStatus[$comment->is_approve] : $commentStatus[0] }}
                                    </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.comment.fields.created_at') }} </th>
                                    <td> {{ $comment->created_at->format(config('constant.date_format.date')) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>