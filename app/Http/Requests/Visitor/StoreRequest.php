<?php

namespace App\Http\Requests\Visitor;

use App\Rules\NoMultipleSpacesRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the visitor is authorized to make this request.
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
            'visitor_type'  => ['required', 'in:' . implode(',', array_keys(config('constant.visitor_types')))],
            'society_id'    => ['required', 'exists:societies,id'],
            'building_id'   => ['required', 'exists:buildings,id'],
            'unit_id'       => ['required', 'exists:units,id'],

            // 'visit_date' => ['nullable', 'required_if:visitor_type,guest', 'required_if:visitor_type,service_man', 'required_if:visitor_type,delivery_man'],
        ];

        $visitorType = $this->input('visitor_type');

        if (in_array($visitorType, ['guest', 'cab', 'service_man', 'delivery_man'])) {
            $rules['name']          = ['required', 'string', 'max:100', new NoMultipleSpacesRule];
            $rules['phone_number']  = ['nullable', 'required_if:visitor_type,guest', 'required_if:visitor_type,service_man', 'numeric', 'digits_between:8,12'];
            $rules['cab_number']    = ['nullable', 'required_if:visitor_type,cab', 'min:4', 'max:4'];
            $rules['keep_package']  = ['nullable', 'required_if:visitor_type,cab'];
            $rules['visitor_note']  = ['nullable'];
            $rules['other_info']    = ['nullable'];
        }

        // if ($visitorType == 'family_member') {
        //     $rules['name']          = ['required', 'string', 'max:100', new NoMultipleSpacesRule];
        //     $rules['phone_number']  = ['required', 'numeric', 'digits_between:8,12', 'unique:resident_family_members,phone_number,NULL,id,deleted_at,NULL'];
        //     $rules['relation']      = ['required'];
        // }

        // if ($visitorType == 'daily_help') {
        //     $rules['name']          = ['required', 'string', 'max:100', new NoMultipleSpacesRule];
        //     $rules['phone_number']  = ['required', 'numeric', 'digits_between:8,12', 'unique:resident_daily_helps,phone_number,NULL,id,deleted_at,NULL'];
        //     $rules['help_type']     = ['required', 'string', 'max:100', new NoMultipleSpacesRule];
        // }

        // if ($visitorType == 'vehicle') {
        //     $rules['vehicle_number']    = ['required', 'string', 'max:255', 'unique:resident_vehicles,vehicle_number,NULL,id,deleted_at,NULL'];
        //     $rules['vehicle_type']      = ['required', 'string', 'max:255'];
        //     $rules['vehicle_model']     = ['required', 'string', 'max:255'];
        //     $rules['vehicle_color']     = ['required', 'string', 'max:255'];
        //     // $rules['vehicle_image']     = ['nullable', 'image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'];
        //     // $rules['gatepass_code']     = ['required'];
        //     // $rules['gatepass_qr_image'] = ['required'];
        // }

        return $rules;
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'type'              => trans('validation.attributes.type'),
            'name'              => trans('validation.attributes.name'),
            'phone_number'     => trans('validation.attributes.mobile_number'),
            'society_id'        => trans('validation.attributes.society'),
            'building_id'       => trans('validation.attributes.society'),
            'unit_id'           => trans('validation.attributes.unit'),
        ];
    }
}
