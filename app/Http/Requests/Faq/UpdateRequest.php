<?php

namespace App\Http\Requests\Faq;

use App\Models\Faq;
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
        $uuid = $this->route('faq');
        $faq = Faq::where('uuid', $uuid)->first();
        $faqId = $faq ? $faq->id : null;

        return [
            'title'             => ['required', 'string', 'max:190', new NoMultipleSpacesRule, Rule::unique('faqs')->ignore($faqId)->whereNull('deleted_at')],
            'short_description' => ['nullable', 'string', new NoMultipleSpacesRule],
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
            'short_description'   => trans('validation.attributes.short_description'),
            'description'   => trans('validation.attributes.description'),
        ];
    }
}
