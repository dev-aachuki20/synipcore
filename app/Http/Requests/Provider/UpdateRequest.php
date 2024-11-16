<?php

namespace App\Http\Requests\Provider;

use App\Models\Post;
use App\Models\User;
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
        $uuid = $this->route('provider');
        $user = User::where('uuid', $uuid)->first();
        $userId = $user ? $user->id : null;

        return [
            'name'              => ['required', 'string', 'max:90', new NoMultipleSpacesRule],
            'email'             => ['required', 'email', 'regex:/^(?!.*[\/]).+@(?!.*[\/]).+\.(?!.*[\/]).+$/i', Rule::unique('users')->ignore($userId)->whereNull('deleted_at')],
            'mobile_number'     => ['required', 'numeric', 'regex:/^[0-9]+$/', 'digits_between:8,12', Rule::unique('users')->ignore($userId)->whereNull('deleted_at')],
            // 'society_id'        => ['required', 'exists:societies,id'],
            'is_featured'       => ['nullable', 'in:1,0'],
            'address'           => ['nullable', 'string'],
            'description'       => ['nullable', 'string'],
            'is_verified'       => ['nullable', 'in:1,0'],
            'profile_image'     => ['nullable', 'image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
            'provider_url'      => ['nullable', 'string'],
            'society_ids'       => ['nullable', 'array'],
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
            'name'          => trans('validation.attributes.name'),
            'email'         => trans('validation.attributes.email'),
            'mobile_number' => trans('validation.attributes.mobile_number'),
            'society_ids'    => trans('validation.attributes.society'),
            'is_featured'   => trans('validation.attributes.is_featured'),
            'address'       => trans('validation.attributes.address'),
            'description'   => trans('validation.attributes.description'),
            'is_verified'   => trans('validation.attributes.is_verified'),
            'profile_image' => trans('validation.attributes.profile_image'),
        ];
    }
}
