<?php

namespace App\Http\Requests\Announcement;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NoMultipleSpacesRule;
use App\Rules\UniquePollOptions;

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
        $rules = [
            'title'                 => ['required', 'string', 'max:190', new NoMultipleSpacesRule],
            'message'               => ['required', 'string'],
            'society_id'            => ['required', 'exists:societies,id'],
            'announcement_type'     => ['required', 'in:' . implode(',', array_keys(config('constant.annuncement_types')))],
            'posted_by'             => ['nullable'],
            'announcment_image*'    => ['nullable', 'image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
            'announcment_image'     => ['array']
        ];

        if ($this->announcement_type == 2) {
            $rules['expire_date']   = ['required', 'date', 'after_or_equal:today'];
            $rules['poll_type']     = ['required', 'string', 'in:' . implode(',', array_keys(config('constant.poll_type')))];
            $rules['options']       = ['required', 'array', new UniquePollOptions];
            $rules['options.*']     = ['required', 'string', 'max:255'];
        }

        return $rules;
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'title'         => trans('validation.attributes.title'),
            'posted_by'     => trans('validation.attributes.posted_by'),
            'announcement_type'   => trans('validation.attributes.announcement_type'),
            'message'       => trans('validation.attributes.message'),
            'society_id'    => trans('validation.attributes.society'),
            'expire_date'   => trans('validation.attributes.expire_date'),
            'poll_type'     => trans('validation.attributes.poll_type'),
        ];
    }
}
