<?php

namespace App\Http\Requests\ServiceCategory;

use App\Models\ServiceCategory;
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
        $uuid = $this->route('service_category');
        $serCat = ServiceCategory::where('uuid', $uuid)->first();
        $serCatId = $serCat ? $serCat->id : null;

        return [
            'title' => ['required', 'string', 'max:190', new NoMultipleSpacesRule, Rule::unique('service_categories')->whereNull('deleted_at')->ignore($serCatId)],
            'status' => ['required', 'in:active,inactive'],
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
