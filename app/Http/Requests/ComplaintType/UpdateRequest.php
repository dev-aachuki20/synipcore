<?php

namespace App\Http\Requests\ComplaintType;

use App\Models\ComplaintType;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NoMultipleSpacesRule;
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
        $typeId = $this->id ?? null;

        return [
            'title'         => ['required', 'string', 'max:190', new NoMultipleSpacesRule],
            'sort_order'    => ['required', 'integer', 'min:1'],

            'id'            => ['required', 'exists:complaint_types,uuid'],
            'slug'          => ['required', 'string', 'max:191', 'unique:complaint_types,slug,' . $typeId . ',uuid,deleted_at,NULL', 'regex:/^\S*$/u', new NoMultipleSpacesRule],


            // 'slug'          => ['required', 'string', 'max:191', Rule::unique('complaint_types', 'slug')->ignore($typeId)->whereNull('deleted_at'), 'regex:/^\S*$/u', new NoMultipleSpacesRule],
            'image'         => ['nullable', 'image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg,gif,svg'],
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
            'sort_order'   => trans('validation.attributes.sort_order'),
            'slug'   => trans('validation.attributes.slug'),
            'image'   => trans('validation.attributes.image'),
        ];
    }
}
