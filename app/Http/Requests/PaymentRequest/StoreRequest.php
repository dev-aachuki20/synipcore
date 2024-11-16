<?php

namespace App\Http\Requests\PaymentRequest;

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
            'title'                 => ['required', 'string', 'max:190', new NoMultipleSpacesRule],
            'due_date'              => ['required', 'date'],
            'amount'                => ['required', 'numeric'],
            'society_id'            => ['required', 'exists:societies,uuid'],
            'building_id'           => ['required', 'exists:buildings,uuid'],
            'unit_id'               => ['required', 'exists:units,uuid'],
            'status'                => ['required', 'in:' . implode(',', array_keys(config('constant.payment_request_status')))],
        ];
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'title'      => trans('validation.attributes.title'),
            'due_date'   => trans('validation.attributes.due_date'),
            'amount'     => trans('validation.attributes.amount'),
            'society_id' => trans('validation.attributes.society'),
            'building_id' => trans('validation.attributes.building'),
            'unit_id'   => trans('validation.attributes.unit'),
            'status'    => trans('validation.attributes.status'),
        ];
    }
}
