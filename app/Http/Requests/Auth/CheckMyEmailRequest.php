<?php

namespace App\Http\Requests\Auth;

use App\Library\Setting;
use Illuminate\Foundation\Http\FormRequest;

class CheckMyEmailRequest extends FormRequest
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
            'email' => "required|email|exists:mst_users,email",
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('validation.required.text', ['attribute' => __('labels.email')]),
            'email.email'    => __('validation.email', ['attribute' => __('labels.email')]),
            'email.exists'   => __('validation.exists', ['attribute' => __('labels.email')])
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = Setting::sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.user')]), $validator->errors(), 400);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
