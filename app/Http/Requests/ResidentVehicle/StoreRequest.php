<?php

namespace App\Http\Requests\ResidentVehicle;

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
        return [
            'vehicle_number'    => ['required', 'unique:resident_vehicles,vehicle_number,NULL,id,deleted_at,NULL', new NoMultipleSpacesRule],
            'vehicle_type'      => ['required', 'string', 'max:255', new NoMultipleSpacesRule],
            'vehicle_model'     => ['required', 'string', 'max:255', new NoMultipleSpacesRule],
            'parking_slot_no'   => ['required', 'string', 'max:255', new NoMultipleSpacesRule],
            'status'            => ['required', 'in:' . implode(',', array_keys(config('constant.status_type.vehicle_status')))],
            'society_id'        => ['required', 'exists:societies,id'],
            'building_id'       => ['nullable', 'required_with:society_id', 'exists:buildings,id'],
            'unit_id'           => ['nullable', 'required_with:building_id', 'exists:units,id'],
            // 'resident_id'       => ['required', 'exists:users,id'],
        ];
    }


    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'vehicle_number'      => trans('validation.attributes.vehicle_number'),
            'vehicle_type'        => trans('validation.attributes.vehicle_type'),
            'vehicle_model'       => trans('validation.attributes.vehicle_model'),
            'parking_slot_no'     => trans('validation.attributes.parking_slot_no'),
            'society_id'          => trans('validation.attributes.society'),
            'building_id'         => trans('validation.attributes.building'),
            'unit_id'             => trans('validation.attributes.unit'),
            'resident_id'         => trans('validation.attributes.resident'),
            'status'              => trans('validation.attributes.status'),
        ];
    }
}
