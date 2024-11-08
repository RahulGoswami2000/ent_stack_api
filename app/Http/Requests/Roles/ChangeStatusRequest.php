<?php

namespace App\Http\Requests\Roles;

use App\Library\Setting;
use Illuminate\Foundation\Http\FormRequest;

class ChangeStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'is_active' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'is_active.required' => __('validation.required.text', ['attribute' => __('labels.roles')]),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = Setting::sendError(__('messages.module_name_not_found', ['moduleName' => __('labels.roles')]), $validator->errors(), 400);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
