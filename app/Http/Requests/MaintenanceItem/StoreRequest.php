<?php

namespace App\Http\Requests\MaintenanceItem;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NoMultipleSpacesRule;
use App\Rules\TitleValidationRule;

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
            'title'             => ['required', 'string', 'max:190', 'unique:maintenance_items,title,NULL,id,deleted_at,NULL', new NoMultipleSpacesRule],
            'description'       => ['nullable', 'string'],
            'duration'          => ['required', 'string', 'in:' . implode(',', array_keys(config('constant.durations')))],
            'budget'            => ['required', 'numeric', 'min:0'],
            'category_id'       => ['required', 'exists:categories,id'],

        ];
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'society_id'         => trans('validation.attributes.society'),
            'year_of'          => trans('validation.attributes.year_of'),
            'category_id'   => trans('validation.attributes.category_id'),
            'maintenance_item_id'    => trans('validation.attributes.maintenance_item'),
            'month'    => trans('validation.attributes.month'),
            'total_budget'    => trans('validation.attributes.total_budget'),
            'comments'    => trans('validation.attributes.comments'),
        ];
    }
}
