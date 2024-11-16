<?php

namespace App\Http\Requests\Guard;

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
            'name'              => ['required', 'max:90', new NoMultipleSpacesRule],
            'email'             => ['nullable', 'email', 'regex:/^(?!.*[\/]).+@(?!.*[\/]).+\.(?!.*[\/]).+$/i', 'unique:users,email,' . $this->id . ',uuid,deleted_at,NULL'],
            'mobile_number'     => ['required', 'numeric', 'regex:/^[0-9]+$/', 'digits_between:8,12', 'unique:users,mobile_number,' . $this->id . ',uuid,deleted_at,NULL'],
            // 'security_pin'      => ['required', 'numeric', 'digits:6', 'unique:users,security_pin,' . $this->id . ',uuid,deleted_at,NULL'],
            'society_id'        => ['required', 'exists:societies,id'],
            'building_id'       => ['nullable', 'exists:buildings,id'],
            'unit_id'           => ['nullable', 'exists:units,id'],
            'description'       => ['nullable', 'string'],
            'guard_duty_status' => ['nullable', 'in:' . implode(',', array_keys(config('constant.status_type.guard_duty_status')))],
            'id'                => ['required', 'exists:users,uuid'],
            // 'is_enabled'        => ['nullable', 'in:1,0'],
            'status'            => ['nullable', 'in:1,0'],

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
            'security_pin'      => trans('validation.attributes.security_pin'),
            'society_id'        => trans('validation.attributes.society'),
            'building_id'       => trans('validation.attributes.building'),
            'unit_id'           => trans('validation.attributes.unit'),
            'description'       => trans('validation.attributes.note'),
            'guard_duty_status' => trans('validation.attributes.guard_duty_status'),
        ];
    }
}
