<?php

namespace App\Http\Requests\ResidentDailyHelp;

use App\Models\ResidentDailyHelp;
use App\Models\User;
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
        $uuid = $this->route('resident_daily_help');
        $resDailyHelp = ResidentDailyHelp::where('uuid', $uuid)->first();
        $resDailyHelpId = $resDailyHelp ? $resDailyHelp->id : null;
        return [
            'name'              => ['required', 'string', 'max:100', new NoMultipleSpacesRule],
            'help_type'         => ['required', 'string', 'max:100', new NoMultipleSpacesRule],
            'phone_number'      => [
                'required',
                'numeric',
                'regex:/^[0-9]+$/',
                'digits_between:8,12',
                Rule::unique('resident_daily_helps', 'phone_number')
                    ->ignore($resDailyHelpId)
                    ->whereNull('deleted_at')
            ],
            // 'resident_id'       => ['required', 'exists:users,id'],
            'society_id'        => ['required', 'exists:societies,uuid'],
            'building_id'       => ['nullable', 'required_with:society_id', 'exists:buildings,uuid'],
            'unit_id'           => ['nullable', 'required_with:building_id', 'exists:units,uuid'],
        ];
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'name'              => trans('validation.attributes.name'),
            'help_type'             => trans('validation.attributes.help_type'),
            'mobile_number'     => trans('validation.attributes.mobile_number'),
            'mobile_verified'   => trans('validation.attributes.mobile_verified'),
            'society_id'        => trans('validation.attributes.society'),
            'resident_id'        => trans('validation.attributes.resident'),
            'building_id'        => trans('validation.attributes.building'),
            'unit_id'        => trans('validation.attributes.unit'),
        ];
    }
}
