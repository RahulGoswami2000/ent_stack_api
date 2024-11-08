<?php

namespace App\Http\Requests\Auth;

use App\Library\Setting;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'password'              => 'required|confirmed|min:' . Setting::$passwordMin,
            'password_confirmation' => 'required|min:' . Setting::$passwordMin,
        ];
    }

    public function messages()
    {
        return [
            'password.required'              => __('validation.required.text', ['attribute' => __('labels.password')]),
            'password.confirmed'             => __('validation.confirmed', ['attribute' => __('labels.password'), 'otherAttribute' => __('labels.confirm_password')]),
            'password.min'                   => __('validation.min.string', ['attribute' => __('labels.password')]),
            'password_confirmation.required' => __('validation.required.text', ['attribute' => __('labels.confirm_password')]),
            'password_confirmation.min'      => __('validation.min.string', ['attribute' => __('labels.confirm_password')]),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = Setting::sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.password')]), $validator->errors(), 400);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
