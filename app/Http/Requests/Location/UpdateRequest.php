<?php

namespace App\Http\Requests\Location;

use App\Rules\NoMultipleSpacesRule;
use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'title'         => ['required', 'string', 'max:190', new NoMultipleSpacesRule],
            'slug'          => ['required', 'string', 'max:191', 'unique:locations,slug,' . $this->id . ',uuid,deleted_at,NULL', 'regex:/^\S*$/u', new NoMultipleSpacesRule],
            'sort_order'    => ['required', 'integer', 'min:1'],
            'scope_id'      => ['required'],
            'parent_id'     => ['nullable', 'exists:locations,uuid'],
            'image'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'id'            => ['required', 'exists:locations,uuid'],
        ];
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'title'        => trans('validation.attributes.title'),
            'slug'         => trans('validation.attributes.slug'),
            'sort_order'   => trans('validation.attributes.sort_order'),
            'scope_id'     => trans('validation.attributes.scope_id'),
            'parent_id'    => trans('validation.attributes.parent_id'),
            'image'        => trans('validation.attributes.image'),
        ];
    }
}
