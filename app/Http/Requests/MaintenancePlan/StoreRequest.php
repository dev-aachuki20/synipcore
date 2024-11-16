<?php

namespace App\Http\Requests\MaintenancePlan;

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
            'society_id'            => ['required', 'exists:societies,id'],
            'year_of'               => ['required', 'integer'],
            // 'category_id'           => ['required', 'exists:categories,id'],
            'total_budget'          => ['required', 'numeric'],

            'item.*.maintenance_item_id'    => ['required', 'exists:maintenance_items,id'],
            'item.*.comments'               => ['nullable'],
            'item.*.month'                  => ['required', 'array'],
            'item.*.month.*'                => ['required', 'in:Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec', 'min:1', 'max:12'],
            'item.*.budget'                 => ['nullable'],
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
            // 'category_id'   => trans('validation.attributes.category_id'),
            'maintenance_item_id'    => trans('validation.attributes.maintenance_item'),
            'month'    => trans('validation.attributes.monthplan'),
            'total_budget'    => trans('validation.attributes.total_budget'),
            'comments'    => trans('validation.attributes.comments'),
        ];
    }
}
