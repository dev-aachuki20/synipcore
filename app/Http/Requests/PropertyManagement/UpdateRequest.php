<?php

namespace App\Http\Requests\PropertyManagement;

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
            'property_item'         => ['required', 'string'],
            'property_type_id'      => ['required', 'exists:property_types,id'],
            'property_code'         => ['required', 'string'],
            'amount'                => ['required', 'numeric'],
            'unit_price'            => ['required', 'numeric'],
            'purchase_date'         => ['required', 'date'],
            'society_id'            => ['required', 'exists:societies,uuid'],
            'building_id'           => ['nullable', 'exists:buildings,uuid'],
            'unit_id'               => ['nullable', 'exists:units,uuid'],
            'description'           => ['nullable', 'string'],
            'location'              => ['nullable', 'string'],
            'allocation'            => ['nullable', 'string'],
            'property_image*'       => ['nullable', 'image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
            'property_image'        => ['array']
        ];
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'property_item' => trans('validation.attributes.maintenance_item_id'),
            'property_type_id'    => trans('validation.attributes.property_type_id'),
            'property_code'     => trans('validation.attributes.code'),
            'amount'        => trans('validation.attributes.amount'),
            'unit_price'    => trans('validation.attributes.unit_price'),
            'purchase_date' => trans('validation.attributes.purchase_date'),
            'society_id'    => trans('validation.attributes.society'),
            'building_id'   => trans('validation.attributes.building'),
            'unit_id'       => trans('validation.attributes.unit'),
            'description'   => trans('validation.attributes.description'),
            'allocation'    => trans('validation.attributes.allocation'),
            'property_image' => trans('validation.attributes.property_image'),
        ];
    }
}
