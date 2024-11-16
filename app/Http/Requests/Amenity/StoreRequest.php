<?php

namespace App\Http\Requests\Amenity;

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
            'title'                 => ['required', 'string', 'max:190', 'unique:amenities,title,NULL,id,deleted_at,NULL', new NoMultipleSpacesRule],
            'description'           => ['nullable', 'string'],
            'society_id'            => ['required', 'exists:societies,uuid'],
            'fee'                   => ['nullable', 'numeric'],
            'capacity'              => ['required', 'integer'],
            'booking_capacity'      => ['required', 'integer'],
            'advance_booking_days'  => ['required', 'integer'],
            'max_days_per_unit'     => ['required', 'integer']
        ];
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'title'   => trans('validation.attributes.title'),
            'description'   => trans('validation.attributes.description'),
            'society_id'   => trans('validation.attributes.society'),
            'fee'   => trans('validation.attributes.fee'),
            'capacity'   => trans('validation.attributes.capacity'),
            'booking_capacity'   => trans('validation.attributes.booking_capacity'),
            'advance_booking_days'   => trans('validation.attributes.advance_booking_days'),
            'max_days_per_unit'   => trans('validation.attributes.max_days_per_unit'),
        ];
    }
}
