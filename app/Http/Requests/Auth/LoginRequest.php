<?php

namespace App\Http\Requests\Auth;

use App\Library\Setting;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'username' => "required",
            'password' => "required|min:" . Setting::$passwordMin . "|max:" . Setting::$passwordMax,
        ];
    }

    public function messages()
    {
        return [
            'username.required' => __('validation.required.text', ['attribute' => __('labels.username')]),
            'password.required' => __('validation.required.text', ['attribute' => __('labels.password')]),
            'password.min'      => __('validation.min.string', ['attribute' => __('labels.password')]),
            'password.max'      => __('validation.max.string', ['attribute' => __('labels.password')]),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = Setting::sendError(__('messages.failed_to_module_name', ['moduleName' => __('labels.login')]), $validator->errors(), 400);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
