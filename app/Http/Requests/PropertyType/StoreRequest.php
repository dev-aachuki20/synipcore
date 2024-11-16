<?php

namespace App\Http\Requests\PropertyType;

use App\Rules\NoMultipleSpacesRule;
use Illuminate\Foundation\Http\FormRequest;


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
            'title' => ['required', 'string', 'max:190', 'unique:property_types,title,NULL,id,deleted_at,NULL', new NoMultipleSpacesRule],
            'code'  => ['required', 'string', 'max:20', 'unique:property_types,code,NULL,id,deleted_at,NULL', new NoMultipleSpacesRule]
        ];
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'title'  => trans('validation.attributes.title'),
            'code'   => trans('validation.attributes.code'),
        ];
    }
}
