<?php

namespace App\Http\Requests\Category;

use App\Models\Category;
use App\Rules\NoMultipleSpacesRule;
use App\Rules\TitleValidationRule;
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
        $uuid = $this->route('category');
        $category = Category::where('uuid', $uuid)->first();
        $categoryId = $category ? $category->id : null;

        return [
            'title'             => ['required', 'string', 'max:190', new NoMultipleSpacesRule, Rule::unique('categories')->ignore($categoryId)->whereNull('deleted_at')],
            'description'       => ['nullable', 'string'],
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
        ];
    }
}
