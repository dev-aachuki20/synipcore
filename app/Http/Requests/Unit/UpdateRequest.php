<?php

namespace App\Http\Requests\Unit;

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
        $rules = [];
        if ($this->id) {
            $rules['id']  = ['required', 'exists:units,uuid'];
        }
        $rules['title']   = ['required', 'string', 'max:190', new NoMultipleSpacesRule];

        if (isset($this->society_id) && !empty($this->society_id)) {
            $rules['society_id']        = ['nullable', 'exists:societies,id'];
            $rules['building_id']       = ['required_with:society_id', 'exists:buildings,id'];
        }
        return $rules;
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'title'           => trans('validation.attributes.title'),
            'society_id'      => trans('validation.attributes.society'),
            'building_id'     => trans('validation.attributes.building'),
        ];
    }
}
