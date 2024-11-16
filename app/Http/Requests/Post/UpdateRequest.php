<?php

namespace App\Http\Requests\Post;

use App\Models\Post;
use App\Rules\NoMultipleSpacesRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $uuid = $this->route('post');
        $post = Post::where('uuid', $uuid)->first();
        $postId = $post ? $post->id : null;

        $rules =  [
            // 'title'             => ['required', 'string', 'max:190', new NoMultipleSpacesRule, Rule::unique('posts')->ignore($postId)],
            // 'slug'              => ['required', 'string', 'max:190', 'regex:/^\S*$/u', new NoMultipleSpacesRule, Rule::unique('posts')->ignore($postId)],
            'post_type'         => ['required', 'in:' . implode(',', array_keys(config('constant.post_types')))],
            'status'            => ['required', 'in:' . implode(',', array_keys(config('constant.status_type.post_status')))],
        ];

        if($this->post_type == 'text'){
            $rules['content'] = ['nullable', 'string'];
        } else if($this->post_type == 'image'){
            $existingImagesCount = $post->postImages()->count();
            $postDeletedImageIds = !empty($this->postDocIds) ? explode(',', $this->postDocIds) : [];

            $reqValidate = 'nullable';
            if($existingImagesCount > 0 && $existingImagesCount == count($postDeletedImageIds) && empty($this->post_image)){
                $reqValidate = 'required';
            } else if($existingImagesCount == 0 && empty($this->post_image)){
                $reqValidate = 'required';
            }
            $rules['post_image'] = [$reqValidate, 'array', 'max:' . config('constant.post_image_max_file_count')];
            $rules['post_image*'] = ['image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'];
        }  else if ($this->post_type == 'video') {
            $haveVideo = $post->postVideo;
            $isremoved = !empty($this->postVideoRemoved) ? true : false;

            $reqVideoValidate = 'nullable';
            $reqVideoUrlValidate = 'nullable';
            if($haveVideo && $isremoved && empty($this->post_video) && empty($this->video_url)){
                $reqVideoValidate = 'required';
                $reqVideoUrlValidate = 'required';
            } else if(!$haveVideo && empty($this->post_video) && empty($this->video_url)){
                $reqVideoValidate = 'required';
                $reqVideoUrlValidate = 'required';
            }

            $rules['post_video'] = [$reqVideoValidate,  'mimes:mp4,mkv,webm,flv,avi,mov', 'max:' . config('constant.video_max_size')];
        
            $rules['video_url'] = [$reqVideoUrlValidate];
        }

        return $rules;
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            // 'title'          => trans('validation.attributes.title'),
            // 'slug'         => trans('validation.attributes.slug'),
            'content'   => trans('validation.attributes.description'),
            'status'   => trans('validation.attributes.status'),
            'post_image' => trans('validation.attributes.post_image'),
        ];
    }
}
