<?php

namespace App\Http\Requests\Resident;

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
        $residentTypes = array_keys(config('constant.resident_types'));
        $rules = [];

        $rules['id']            = ['required', 'exists:users,uuid'];
        $rules['email']         = ['required', 'email', 'regex:/^(?!.*[\/]).+@(?!.*[\/]).+\.(?!.*[\/]).+$/i', 'unique:users,email,' . $this->id . ',uuid,deleted_at,NULL'];
        $rules['mobile_number'] = ['required', 'numeric', 'regex:/^[0-9]+$/', 'digits_between:8,12', 'unique:users,mobile_number,' . $this->id . ',uuid,deleted_at,NULL'];
        $rules['name']          = ['required', 'string', 'max:90', new NoMultipleSpacesRule];

        // if (isset($this->society_id) && !empty($this->society_id)) {
        //     $rules['society_id']    = ['nullable', 'exists:societies,id'];
        //     $rules['building_id']   = ['required_with:society_id', 'exists:buildings,id'];
        //     $rules['unit_id']       = ['required_with:building_id', 'exists:units,id'];
        // }

        $rules['society_id']    = ['required', 'exists:societies,id'];
        $rules['building_id']   = ['required', 'exists:buildings,id'];
        $rules['unit_id']       = ['required', 'exists:units,id'];

        $rules['type']              = [
            'required',
            function ($attribute, $value, $fail) use ($residentTypes) {
                if (!in_array($value, $residentTypes)) {
                    $fail('The selected ' . $attribute . ' is not correct.');
                }
            }
        ];
        $rules['is_verified']       = ['nullable', 'in:1,0'];
        $rules['description']       = ['nullable', 'string'];

        return $rules;
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'email'         => trans('validation.attributes.email'),
            'mobile_number' => trans('validation.attributes.mobile_number'),
            'password'      => trans('validation.attributes.password'),
            'name'          => trans('validation.attributes.name'),
            'society_id'    => trans('validation.attributes.society'),
            'building_id'   => trans('validation.attributes.building'),
            'unit_id'       => trans('validation.attributes.unit'),
            'type'          => trans('validation.attributes.type'),
            'is_verified'   => trans('validation.attributes.is_verified'),
            'description'   => trans('validation.attributes.description'),
        ];
    }
}
