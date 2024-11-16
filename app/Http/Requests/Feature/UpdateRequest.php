<?php

namespace App\Http\Requests\Feature;

use App\Models\Feature;
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
        $uuid = $this->route('feature');
        $feature = Feature::where('uuid', $uuid)->first();
        $featureId = $feature ? $feature->id : null;

        return [
            'title' => ['required', 'string', 'max:190', new NoMultipleSpacesRule, Rule::unique('features')->ignore($featureId)->whereNull('deleted_at')]
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
        ];
    }
}
