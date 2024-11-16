<?php

namespace App\Http\Requests\User;

use App\Models\User;
use App\Rules\NoMultipleSpacesRule;
use App\Rules\TitleValidationRule;
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
        $user = User::where('uuid', $this->uuid)->first();
        $userId = $user ? $user->id : null;
        return [
            'name'              => ['required', 'string', 'max:100', new NoMultipleSpacesRule],
            'email'             => ['nullable', 'email', 'regex:/^(?!.*[\/]).+@(?!.*[\/]).+\.(?!.*[\/]).+$/i', Rule::unique('users')->whereNull('deleted_at')->ignore($userId)],
            'mobile_number'     => ['nullable', 'numeric', 'digits_between:8,12', Rule::unique('users')->whereNull('deleted_at')->ignore($userId)],
            'mobile_verified'   => ['nullable', 'in:1,0'],
            'language_id'       => ['required', 'string', 'max:20'],
            'roles'             => ['nullable', 'array'],
            'roles.*'           => ['exists:roles,id'],
            // 'society_id'        => ['nullable', 'exists:societies,id'],
            'profile_image'     => ['nullable', 'image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
            'description'       => ['nullable', 'string'],
            // 'is_enabled'        => ['nullable', 'in:1,0'],
            'status'        => ['nullable', 'in:1,0'],
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
