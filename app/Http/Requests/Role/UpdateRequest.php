<?php

namespace App\Http\Requests\Role;

use App\Rules\NoMultipleSpacesRule;
use App\Rules\TitleValidationRule;
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

        $id = $this->route('role')->id;
        return [
            'role_name'     => ['required', 'unique:roles,name,' . $id, 'max:255', new NoMultipleSpacesRule],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ];
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'role_name'         => trans('validation.attributes.role'),
            'permissions'       => trans('validation.attributes.permissions'),
        ];
    }
}
