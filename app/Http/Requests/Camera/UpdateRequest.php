<?php

namespace App\Http\Requests\Camera;

use App\Models\Camera;
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

        $uuid = $this->route('camera');
        $camera = Camera::where('uuid', $uuid)->first();
        $cameraId = $camera ? $camera->id : null;

        return [
            'camera_id'     => ['required', 'string', 'max:20', Rule::unique('cameras')->ignore($cameraId)->whereNull('deleted_at'), new NoMultipleSpacesRule],
            'lacated_at'    => ['nullable', 'string'],
            'society_id'    => ['required', 'exists:societies,uuid'],
            'building_id'   => ['nullable', 'exists:buildings,uuid'],
            'unit_id'       => ['nullable', 'exists:units,uuid'],
            'description'   => ['nullable'],
            'status'        => ['nullable'],
        ];
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [
            'camera_id'     => trans('validation.attributes.camera'),
            'lacated_at'       => trans('validation.attributes.lacated'),
            'society_id'    => trans('validation.attributes.society'),
            'building_id'   => trans('validation.attributes.building'),
            'unit_id'       => trans('validation.attributes.unit'),
            'description'   => trans('validation.attributes.description'),
            'status'        => trans('validation.attributes.status'),
        ];
    }
}
