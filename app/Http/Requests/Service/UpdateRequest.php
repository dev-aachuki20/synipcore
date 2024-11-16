<?php

namespace App\Http\Requests\Service;

use App\Models\Service;
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
        $uuid = $this->route('service');
        $service = Service::where('uuid', $uuid)->first();
        $serviceId = $service ? $service->id : null;
        return [
            'title'                 => ['required', 'string', 'max:190', new NoMultipleSpacesRule, Rule::unique('service_categories')->ignore($serviceId)->whereNull('deleted_at')],
            'slug'                  => ['required', 'string', 'max:191', 'unique:services,slug,' . $this->id . ',uuid,deleted_at,NULL', 'regex:/^\S*$/u', new NoMultipleSpacesRule],
            'sort_order'            => ['required', 'integer', 'min:1'],
            'image'                 => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'description'           => ['nullable', 'string', new NoMultipleSpacesRule],
            'is_featured'           => ['nullable', 'in:1,0'],
            'user_id'               => ['required', 'exists:users,id'],
            'id'                    => ['required', 'exists:services,uuid'],
            'service_category_id'   => ['required', 'exists:service_categories,id'],
            'service_url'           => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'slug'          => trans('validation.attributes.slug'),
            'title'         => trans('validation.attributes.title'),
            'sort_order'    => trans('validation.attributes.sort_order'),
            'image'         => trans('validation.attributes.image'),
            'description'   => trans('validation.attributes.description'),
            'is_featured'   => trans('validation.attributes.is_featured'),
            'user_id'       => trans('validation.attributes.provider'),
            'service_category_id' => trans('validation.attributes.service_category_id'),
        ];
    }
}
