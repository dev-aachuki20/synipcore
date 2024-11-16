<?php

namespace App\Http\Requests\User;

use App\Rules\NoMultipleSpacesRule;
use App\Rules\TitleValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


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
            'name'              => ['required', 'string', 'max:100', new NoMultipleSpacesRule],
            'email'             => ['required', 'email', 'regex:/^(?!.*[\/]).+@(?!.*[\/]).+\.(?!.*[\/]).+$/i', 'unique:users,email,NULL,id,deleted_at,NULL'],
            'mobile_number'     => ['required', 'numeric', 'regex:/^[0-9]+$/', 'digits_between:8,12', 'unique:users,mobile_number,NULL,id,deleted_at,NULL'],
            'mobile_verified'   => ['nullable', 'in:1,0'],
            'password'          => ['required', 'string', 'min:6', 'max:8' /* ,'regex:/^(?!.*\s)(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/' */],
            'language_id'       => ['required', 'string', 'max:20'],
            'roles'             => ['nullable', 'array'],
            'roles.*'           => ['exists:roles,id'],
            // 'society_id'        => ['nullable', 'exists:societies,id'],
            'profile_image'     => ['nullable', 'image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
            'description'       => ['nullable', 'string'],
            // 'is_enabled'        => ['nullable', 'in:1,0'],
            'status'            => ['nullable', 'in:1,0'],
            'society_ids'       => ['required', 'array'],
            'society_ids.*'     => ['exists:societies,id'],

        ];
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'name'              => trans('validation.attributes.name'),
            'email'             => trans('validation.attributes.email'),
            'mobile_number'     => trans('validation.attributes.mobile_number'),
            'mobile_verified'   => trans('validation.attributes.mobile_verified'),
            'password'          => trans('validation.attributes.password'),
            'language_id'       => trans('validation.attributes.language'),
            'roles'             => trans('validation.attributes.roles'),
            'society_id'        => trans('validation.attributes.society'),
            'profile_image'     => trans('validation.attributes.profile_image'),
            'description'       => trans('validation.attributes.description'),
        ];
    }
}
