<?php

namespace App\Http\Requests\PropertyType;

use App\Models\PropertyType;
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
        $uuid = $this->route('prpoertyType');
        $propertyType = PropertyType::where('uuid', $uuid)->first();
        $propertyTypeId = $propertyType ? $propertyType->id : null;

        return [
            'title' => ['required', 'string', 'max:190', new NoMultipleSpacesRule, Rule::unique('property_types')->ignore($propertyTypeId)->whereNull('deleted_at')],
            'code'  => ['required', 'string', 'max:190', new NoMultipleSpacesRule, Rule::unique('property_types')->ignore($propertyTypeId)->whereNull('deleted_at')]
        ];
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'title'  => trans('validation.attributes.title'),
            'code'   => trans('validation.attributes.code'),
        ];
    }
}
