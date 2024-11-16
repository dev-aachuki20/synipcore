<?php

namespace App\Http\Requests\Society;

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
            'name'              => ['required', 'string', 'max:190', new NoMultipleSpacesRule],
            'address'           => ['required', 'string', 'max:210', new NoMultipleSpacesRule],
            'city'              => ['required', 'exists:locations,id'],
            'latitude'          => ['required', 'numeric', 'between:-90,90', 'regex:/^-?([0-8]?[0-9](\.\d{1,7})?|90(\.0{1,7})?)$/'],
            'longitude'         => ['required', 'numeric', 'between:-180,180', 'regex:/^-?((1[0-7][0-9]|[0-9]?[0-9])(\.\d{1,7})?|180(\.0{1,6})?)$/'],
            'key.*'             => ['nullable', 'string'],
            'value.*'           => ['nullable', 'string'],
            'district'          => ['required', 'exists:locations,id'],
            'fire_alert'        => ['nullable', 'in:1,0'],
            'lift_alert'        => ['nullable', 'in:1,0'],
            'animal_alert'      => ['nullable', 'in:1,0'],
            'visitor_alert'     => ['nullable', 'in:1,0'],
        ];
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'name'              => trans('validation.attributes.title'),
            'address'           => trans('validation.attributes.address'),
            'city'              => trans('validation.attributes.city'),
            'latitude'          => trans('validation.attributes.latitude'),
            'longitude'         => trans('validation.attributes.longitude'),
            'key'               => trans('validation.attributes.key'),
            'value'             => trans('validation.attributes.value'),
            'district'          => trans('validation.attributes.district'),
            'fire_alert'        => trans('validation.attributes.fire_alert'),
            'lift_alert'        => trans('validation.attributes.lift_alert'),
            'animal_alert'      => trans('validation.attributes.animal_alert'),
            'visitor_alert'     => trans('validation.attributes.visitor_alert'),
        ];
    }
}
