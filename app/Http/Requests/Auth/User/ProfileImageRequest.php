<?php

namespace App\Http\Requests\Auth\User;

use App\Library\Setting;
use Illuminate\Foundation\Http\FormRequest;

class ProfileImageRequest extends FormRequest
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
            'profile_image' => 'nullable|mimes: ' . Setting::$imageMimes,
        ];
    }

    public function messages()
    {
        return [
            'profile_image.mimes' => __('validation.mimes', ['attribute' => __('labels.profile_image')]),
            'profile_image.max'   => __('validation.max', ['attribute' => __('labels.profile_image')]),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $id = $this->route()->parameter('id');
        if (empty($id)) {
            $response = Setting::sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.profile_image')]), $validator->errors(), 400);
        } else {
            $response = Setting::sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.profile_image')]), $validator->errors(), 400);
        }

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
