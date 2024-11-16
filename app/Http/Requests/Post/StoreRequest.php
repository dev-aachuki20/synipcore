<?php

namespace App\Http\Requests\Post;

use App\Rules\NoMultipleSpacesRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\RequiredIf;

class StoreRequest extends FormRequest
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

        $rules = [
            // 'title'             => ['required', 'string', 'max:190', 'unique:posts,title,NULL,id,deleted_at,NULL', new NoMultipleSpacesRule],
            // 'slug'              => ['required', 'string', 'max:190', 'regex:/^\S*$/u', new NoMultipleSpacesRule, 'unique:posts,slug'],
            
            'post_type'         => ['required', 'in:' . implode(',', array_keys(config('constant.post_types')))],
            'status'            => ['required', 'in:' . implode(',', array_keys(config('constant.status_type.post_status')))]
        ];

        if($this->post_type == 'text'){
            $rules['content'] = ['required', 'string'];
        } else if($this->post_type == 'image'){
            $rules['post_image*'] = ['required', 'image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'];
            $rules['post_image'] = ['required', 'array', 'max:' . config('constant.post_image_max_file_count')];
        }  else if ($this->post_type == 'video') {
            $rules['post_video'] = ['required_if:video_url,null',  'mimes:mp4,mkv,webm,flv,avi,mov', 'max:' . config('constant.video_max_size')];
        
            $rules['video_url'] = ['nullable'];
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
