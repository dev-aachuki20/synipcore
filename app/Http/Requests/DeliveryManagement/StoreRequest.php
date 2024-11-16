<?php

namespace App\Http\Requests\DeliveryManagement;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NoMultipleSpacesRule;
use App\Rules\TitleValidationRule;

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
            'subject'           => ['required', 'string', 'max:255', new NoMultipleSpacesRule],
            'message'           => ['required', 'string'],
            'delivery_type_id'  => ['required', 'exists:delivery_types,id'],
            'society_id'        => ['required', 'exists:societies,uuid'],
            'building_id'       => ['nullable', 'exists:buildings,uuid'],
            'unit_id'           => ['nullable', 'exists:units,uuid'],
            'notes'             => ['nullable', 'string'],
            'status'            => ['required', 'in:' . implode(',', array_keys(config('constant.status_type.delivery_status')))],

            // 'user_id'   => ['nullable','exists:users,id'],
            // 'notify' => 'required|string',
            // 'notes' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'subject'   => trans('validation.attributes.subject'),
            'message'   => trans('validation.attributes.message'),
            'delivery_type_id'   => trans('validation.attributes.delivery_type_id'),
            'society_id'   => trans('validation.attributes.society'),
            'building_id '   => trans('validation.attributes.building'),
            'unit_id '   => trans('validation.attributes.unit'),
            'status'   => trans('validation.attributes.status'),
        ];
    }
}
