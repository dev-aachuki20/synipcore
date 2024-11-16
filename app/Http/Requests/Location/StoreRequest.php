<?php

namespace App\Http\Requests\Location;

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
        // $locationValueTypes = config('constant.location_meta_keys.value');
        return [
            'title'      => ['required', 'string', 'max:190', new NoMultipleSpacesRule],
            'slug'       => ['required', 'string', 'max:190', 'unique:locations,slug,NULL,id,deleted_at,NULL', 'regex:/^\S*$/u', new NoMultipleSpacesRule],
            'sort_order' => ['required', 'integer', 'min:1'],
            'scope_id'   => ['required'],
            'parent_id'  => ['nullable', 'exists:locations,uuid'],
            'image'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];

        // $rules['title']             = ['required', 'regex:/^[a-zA-Z\s]+$/','string', 'max:50', new NoMultipleSpacesRule];
        /* $rules['key.*']             = ['nullable', 'string'];
        $rules['value.*']           = ['nullable', 'string','exists:',
            function ($attribute, $value, $fail) use ($locationValueTypes) {
                if (!in_array($value, $locationValueTypes)) {
                    $fail('The selected ' . $attribute . ' is not correct.');
                }
            }
        ]; */
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
