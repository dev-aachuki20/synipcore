<?php

namespace App\Http\Requests\Complaint;

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
        $rules = [];
        if ($this->id) {
            $rules['id'] = ['required', 'exists:complaints,uuid'];
            $rules['slug'] = ['required', 'string', 'max:191', 'unique:complaints,slug,' . $this->id . ',uuid,deleted_at,NULL', 'regex:/^\S*$/u', new NoMultipleSpacesRule];
        } else {
            $rules['slug'] = ['required', 'string', 'max:191', 'unique:complaints,slug,NULL,id,deleted_at,NULL', 'regex:/^\S*$/u', new NoMultipleSpacesRule];
        }

        $rules['title']             = ['required', 'string', 'max:190', new NoMultipleSpacesRule];
        $rules['sort_order']        = ['required', 'integer', 'min:1'];
        $rules['image']             = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'];

        return $rules;
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'title'   => trans('validation.attributes.title'),
            'sort_order'   => trans('validation.attributes.sort_order'),
            'slug'   => trans('validation.attributes.slug'),
            'image'   => trans('validation.attributes.image'),
        ];
    }
}
