<div class="row">
    <input type="hidden" name="postDocIds" id="postDocIds">
    <input type="hidden" name="postVideoRemoved" id="postVideoRemoved">

    <div class="col-lg-12">
        <div class="form-group">
            <label for="post_type" class="form-label">@lang('cruds.post.fields.post_type')<span class="required">*</span></label>
            <select id="post_type" name="post_type" class="form-control h-auto">
                @foreach (config('constant.post_types') as $key => $val)
                    <option value="{{ $key }}" {{ isset($post) && !empty($post->post_type) && $post->post_type == $key ? 'Selected' : '' }}>{{ $val }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-lg-12 {{ (isset($post) ? ($post->post_type == 'text' ? '' : 'd-none')  : '') }} post-content-fields" id="post_text_main">
        <div class="form-group">
            <label for="title" class="form-label">@lang('cruds.post.fields.description')<span class="required">*</span></label>
            <textarea name="content" id="content" class="form-control" cols="30" rows="10" placeholder="@lang('cruds.post.fields.description')">{{ isset($post) && !empty($post->content) ? $post->content : '' }}</textarea>
        </div>
    </div>
    
    <div class="col-md-12 {{ (isset($post) ? ($post->post_type == 'image' ? '' : 'd-none')  : 'd-none') }} post-content-fields" id="post_image_main">
        <div class="form-group">
            <label for="post_image" class="form-label">{{ trans('global.image') }}</label>
            <div id="post_image" class="form-control dropzone">
                <div class="dz-default dz-message">{{ trans('global.drag_drop') }}</div>
            </div>
            <div id="max-upload-image" style="color: rgb(208, 63, 63);"></div>
        </div>
    </div>

    <div class="col-md-12 {{ (isset($post) ? ($post->post_type == 'video' ? '' : 'd-none')  : 'd-none') }} post-content-fields" id="post_video_main">
        <div class="form-group">
            <label for="video_url" class="form-label">{{ trans('cruds.post.fields.video_url') }}</label>
            <input type="text" name="video_url" id="video_url" class="form-control" value="{{ isset($post) && !empty($post->video_url) ? $post->video_url : '' }}">
        </div>
        <div class="form-group">
            <label for="post_image" class="form-label">{{ trans('cruds.post.fields.video') }}</label>
            <input name="post_video" type="file" class="dropify" id="post_video" data-default-file="{{ isset($post) && !empty($post->post_video_url) ? $post->post_video_url : '' }}" data-allowed-file-extensions="mp4 mkv webm flv avi mov" accept="video/mp4, video/mkv, video/flv, video/avi, video/mov"  data-show-loader="true" data-errors-position="outside"  />
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <label for="type" class="form-label">@lang('cruds.post.fields.status')<span class="required">*</span></label>
            <select id="type" name="status" class="form-control h-auto">
                <option value="">@lang('global.select') @lang('cruds.post.fields.status')</option>
                @foreach (config('constant.status_type.post_status') as $key => $val)
                    <option value="{{ $key }}"
                        {{ isset($post) && !empty($post->status) && $post->status == $key ? 'Selected' : '' }}>
                        {{ $val }}</option>
                @endforeach
            </select>
        </div>
    </div>


    <div class="col-lg-12">
        <div class="bottombtn-group">
            <button class="btn btn-primary submitBtn"
                type="submit">{{ isset($post) && !empty($post) ? trans('global.update') : trans('global.save') }}
            </button>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-danger">{{ trans('global.back') }}</a>
        </div>
    </div>
</div>
