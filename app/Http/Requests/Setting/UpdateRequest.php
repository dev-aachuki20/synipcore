<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        abort_if((Gate::denies('setting_edit')), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'site_title'        => 'required',
            'site_logo'         => 'image|mimes:jpeg,png,jpg,PNG,JPG',
            'favicon'           => 'image|mimes:jpeg,png,jpg,PNG,JPG',
            'terms_condition'   => 'mimes:pdf',
            'privacy_policy'    => 'mimes:pdf'
        ];
    }


    public function messages()
    {
        return [
            'site_logo.image' => 'The site logo must be an image.',
            'site_logo.mimes' => 'The site logo must be jpeg,png,jpg,PNG,JPG.',
            'favicon.image' => 'The favicon must be an image.',
            'favicon.mimes' => 'The favicon must be jpeg,png,jpg,PNG,JPG.',
        ];
    }
}
