<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NoMultipleSpacesRule;

class RegisterRequest extends FormRequest
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
            'name'                  => ['required', 'string', 'max:255', new NoMultipleSpacesRule],
            'email'                 => ['required', 'email', 'regex:/^(?!.*[\/]).+@(?!.*[\/]).+\.(?!.*[\/]).+$/i', 'unique:users,email,NULL,id,deleted_at,NULL'],
            'mobile_number'         => ['required', 'numeric', 'digits_between:10,15', 'unique:users,mobile_number,NULL,id,deleted_at,NULL'],
            'password'              => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'same:password', 'string', 'min:8'],
            'profile_image'         => ['nullable', 'image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],

            'location_id'           => ['required', 'exists:locations,id'],
            'district_id'           => ['required', 'exists:locations,id'],
            'society_id'            => ['required', 'exists:societies,id'],
            'building_id'           => ['required', 'exists:buildings,id'],
            'unit_id'               => ['required', 'exists:units,id'],
        ];

        return $rules;
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'name'   => trans('validation.attributes.name'),
            'email'   => trans('validation.attributes.email'),
            'mobile_number'   => trans('validation.attributes.mobile_number'),
            'password'   => trans('validation.attributes.password'),
            'password_confirmation'   => trans('validation.attributes.password_confirmation'),
            'profile_image'   => trans('validation.attributes.profile_image'),
            'location_id'   => trans('validation.attributes.location_id'),
            'district_id'   => trans('validation.attributes.district_id'),
            'society_id'   => trans('validation.attributes.society'),
            'building_id'   => trans('validation.attributes.building'),
            'unit_id'   => trans('validation.attributes.unit'),
        ];
    }
}
