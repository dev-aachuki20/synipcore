<?php

namespace App\Http\Requests\DeliveryType;

use App\Models\DeliveryType;
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

        $uuid = $this->route('delivery_type');
        $type = DeliveryType::where('uuid', $uuid)->first();
        $typeId = $type ? $type->id : null;
        return [
            // 'title'         => ['required_without:other', 'string', 'max:255'],
            // 'other'         => ['required_if:title,0', 'max:255', new NoMultipleSpacesRule],
            'title'             => ['required', 'string', 'max:190', new NoMultipleSpacesRule, Rule::unique('delivery_types')->ignore($typeId)->whereNull('deleted_at')],
            'description'   => ['required', 'string'],
            'status'        => ['nullable', 'integer', 'in:' . implode(',', array_keys(config('constant.status')))],
            'notify_user'   => ['required', 'in:' . implode(',', array_keys(config('constant.notify_user')))],
            'due_payment'   => ['nullable', 'integer', 'in:0,1'],

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
            // 'other'   => trans('validation.attributes.other'),
            'description'   => trans('validation.attributes.description'),
            'status'   => trans('validation.attributes.status'),
            'notify_user'   => trans('validation.attributes.notify_user'),
        ];
    }
}
