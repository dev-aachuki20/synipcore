<?php

namespace App\Http\Requests\PaymentMethod;

use App\Rules\NoMultipleSpacesRule;
use Illuminate\Foundation\Http\FormRequest;


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
            'title'             => ['required', 'string', 'max:190', 'unique:payment_methods,title,NULL,id,deleted_at,NULL', new NoMultipleSpacesRule],
            'slug'              => ['required', 'string', 'max:190', 'regex:/^\S*$/u', new NoMultipleSpacesRule, 'unique:payment_methods,slug,NULL,id,deleted_at,NULL'],
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
