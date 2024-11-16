<?php

namespace App\Http\Requests\PaymentMethod;

use App\Models\PaymentMethod;
use App\Models\Post;
use App\Rules\NoMultipleSpacesRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


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
        $uuid = $this->route('payment_method');
        $payment_method = PaymentMethod::where('uuid', $uuid)->first();
        $paymentMethodId = $payment_method ? $payment_method->id : null;

        return [
            'title'             => ['required', 'string', 'max:190', new NoMultipleSpacesRule, Rule::unique('payment_methods')->ignore($paymentMethodId)->whereNull('deleted_at')],
            'slug'              => ['required', 'string', 'max:190', 'regex:/^\S*$/u', new NoMultipleSpacesRule, Rule::unique('payment_methods')->ignore($paymentMethodId)->whereNull('deleted_at')],
            'method_type'       => ['required', 'in:' . implode(',', array_keys(config('constant.status_type.payment_method_type')))],
            'status'            => ['required', 'in:' . implode(',', array_keys(config('constant.status_type.payment_method_status')))],
        ];
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'title'         => trans('validation.attributes.title'),
            'slug'          => trans('validation.attributes.slug'),
            'method_type'   => trans('validation.attributes.method_type'),
            'status'    => trans('validation.attributes.status'),
        ];
    }
}
