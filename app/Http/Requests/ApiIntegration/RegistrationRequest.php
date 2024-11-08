<?php

namespace App\Http\Requests\ApiIntegration;

use App\Library\Setting;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
        \Log::info('----------------------');
        \Log::info('Registration', $this->all());
        \Log::info('----------------------');

        $id = $this->route()->parameter('id') ?? null;
        return [
            'profile_image' => "nullable|mimes: " . Setting::$imageMimes,
            'first_name'    => "required|min:" . Setting::$nameMin . "|max:" . Setting::$nameMax,
            'last_name'     => "required|min:" . Setting::$nameMin . "|max:" . Setting::$nameMax,
            'mobile_no'     => "required|min:" . Setting::$mobMin . "|max:" . Setting::$mobMax . "|regex:" . Setting::$mobRegex,
            'email'         => "required|email|min:" . Setting::$emailMin . "|max:" . Setting::$emailMax . "|unique:mst_users,email,$id",
            'password'      => "required|min:" . Setting::$passwordMin . "|max:" . Setting::$passwordMax,
        ];
    }

    public function messages()
    {
        return [
            'profile_image.max'          => __('validation.max.file', ['attribute' => __('labels.profile_pic')]),
            'first_name.required'        => __('validation.required.text', ['attribute' => __('labels.first_name')]),
            'last_name.required'         => __('validation.required.text', ['attribute' => __('labels.last_name')]),
            'first_name.min'             => __('validation.min.string', ['attribute' => __('labels.first_name')]),
            'last_name.min'              => __('validation.min.string', ['attribute' => __('labels.last_name')]),
            'first_name.max'             => __('validation.max.string', ['attribute' => __('labels.first_name')]),
            'last_name.max'              => __('validation.max.string', ['attribute' => __('labels.last_name')]),
            'email.required'             => __('validation.required.text', ['attribute' => __('labels.email')]),
            'mobile_no.required'         => __('validation.required.text', ['attribute' => __('labels.mobile_no')]),
            'password.required'          => __('validation.required.text', ['attribute' => __('labels.password')]),
            'password.min'               => __('validation.min.string', ['attribute' => __('labels.password')]),
            'password.max'               => __('validation.max.string', ['attribute' => __('labels.password')]),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $id = $this->route()->parameter('id');
        if (empty($id)) {
            $response = Setting::sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.registration')]), $validator->errors(), 400);
        } else {
            $response = Setting::sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.registration')]), $validator->errors(), 400);
        }

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
