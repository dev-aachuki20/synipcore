<div class="modal fade show" id="ViewPost" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-modal="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">{{ trans('global.show') }}
                    {{ trans('cruds.post.fields.detail') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="normal_width_table">
                        <table class="table table-striped mb-0">
                            <tbody>
                                <tr>
                                    <th style="width:150px;"> {{ trans('cruds.post.fields.post_type') }} </th>
                                    <td>
                                        <div class="show-fulldescription"> {{ $post->post_type ? ucfirst($post->post_type) : 'N/A' }} </div>
                                    </td>
                                </tr>
                                @if ($post->post_type == 'text')
                                    <tr>
                                        <th style="width:150px;"> {{ trans('cruds.post.fields.description') }} </th>
                                        <td>
                                            <div class="show-fulldescription"> {!! $post->content ?? 'N/A' !!} </div>
                                        </td>
                                    </tr>
                                @elseif($post->post_type == 'image')
                                    <tr>
                                        <th style="width:150px;"> {{ trans('cruds.post.fields.images') }} </th>
                                        <td>
                                            @if($post->postImages && $post->postImages->isNotEmpty())
                                                <div class="post-images">
                                                    @foreach($post->postImages as $image)
                                                        <img src="{{ $image->file_url }}" alt="Post Image" class="img-thumbnail" style="max-width: 150px; margin-right: 10px;">
                                                    @endforeach
                                                </div>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                @elseif($post->post_type == 'video')
                                    <tr>
                                        <th style="width:150px;"> {{ trans('cruds.post.fields.video') }} </th>
                                        <td>
                                            <div class="show-fulldescription">
                                                <a href="{{ $post->post_video_url ? $post->post_video_url : $post->video_url }}" target="_blank">{{ trans('cruds.post.fields.video_link_text') }}</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                
                                <tr>
                                    <th> {{ trans('cruds.post.fields.created_by') }} </th>
                                    <td> {{ $post->user->name ?? 'N/A' }} </td>
                                </tr>
                                

                                <tr>
                                    <th> {{ trans('cruds.post.fields.total_comments') }} </th>
                                    <td> {{ $post->comments->count() ?? 0 }} </td>
                                </tr>

                                <tr>
                                    <th> {{ trans('cruds.post.fields.total_views') }} </th>
                                    <td> 0 </td>
                                </tr>

                                <tr>
                                    <th> {{ trans('cruds.post.fields.total_likes') }} </th>
                                    <td> 0 </td>
                                </tr>

                                <tr>
                                    <th> {{ trans('cruds.post.fields.total_dislikes') }} </th>
                                    <td> 0 </td>
                                </tr>

                                <tr>
                                    <th> {{ trans('cruds.post.fields.status') }} </th>
                                    <td>
                                        @php
                                            $postStatus = config('constant.status_type.post_status');
                                        @endphp
                                         {{ isset($postStatus[$post->status]) ? $postStatus[$post->status] : $postStatus['draft'] }}
                                    </td>
                                </tr>

                                <tr>
                                    <th> {{ trans('cruds.comment.fields.created_at') }} </th>
                                    <td> {{ $post->created_at->format(config('constant.date_format.date')) }}
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
