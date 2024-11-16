<?php

namespace App\Http\Requests\MaintenancePlan;

use App\Models\Category;
use App\Models\MaintenanceItem;
use App\Models\MaintenancePlan;
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





            //   'item.*.maintenance_item_id'    => ['required', 'array'],
            //     'item.*.maintenance_item_id.*'  => ['required', 'integer'],
            //     'item.*.month'                  => ['required', 'array'],
            //     'item.*.month.*'                => ['required', 'in:Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec'],
            //     'item.*.comments'               => ['required', 'array'],
            //     'item.*.comments.*'             => ['string'],
            //     'item.*.budget'                 => ['required', 'numeric'],


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
            'month'    => trans('validation.attributes.month'),
            'total_budget'    => trans('validation.attributes.total_budget'),
            'comments'    => trans('validation.attributes.comments'),
        ];
    }
}
