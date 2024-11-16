<?php

namespace App\Http\Requests\Building;

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
            'title'      => ['required', 'string', 'max:190', new NoMultipleSpacesRule],
            'society_id' => ['nullable', 'exists:societies,id'],
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
            'society_id'   => trans('validation.attributes.society'),
        ];
    }
}
