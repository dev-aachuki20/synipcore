<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NoMultipleSpacesRule;

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
        return [
            'title'                 => ['required', 'string', 'max:190', 'unique:services,title,NULL,id,deleted_at,NULL', new NoMultipleSpacesRule],
            'slug'                  => ['required', 'string', 'max:191', 'unique:services,slug,NULL,id,deleted_at,NULL', 'regex:/^\S*$/u', new NoMultipleSpacesRule],
            'sort_order'            => ['required', 'integer', 'min:1'],
            'image'                 => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'description'           => ['nullable', 'string', new NoMultipleSpacesRule],
            'is_featured'           => ['nullable', 'in:1,0'],
            'user_id'               => ['required', 'exists:users,id'], //provider
            'service_category_id'   => ['required', 'exists:service_categories,id'],
            'service_url'           => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'slug'          => trans('validation.attributes.slug'),
            'title'         => trans('validation.attributes.title'),
            'sort_order'    => trans('validation.attributes.sort_order'),
            'image'         => trans('validation.attributes.image'),
            'description'   => trans('validation.attributes.description'),
            'is_featured'   => trans('validation.attributes.is_featured'),
            'user_id'       => trans('validation.attributes.provider'),
            'service_category_id' => trans('validation.attributes.service_category_id'),

        ];
    }
}
