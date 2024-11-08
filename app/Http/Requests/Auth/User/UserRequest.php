<?php

namespace App\Http\Requests\Auth\User;

use App\Library\Setting;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'first_name'    => "required|min:" . Setting::$nameMin . "|max:" . Setting::$nameMax,
            'last_name'     => "required|min:" . Setting::$nameMin . "|max:" . Setting::$nameMax,
            'email'         => "required|email|unique:mst_users,email|min:" . Setting::$emailMin . "|max:" . Setting::$emailMax,
            'mobile_no'     => "required|min:" . Setting::$mobMin . "|max:" . Setting::$mobMax . "|regex:" . Setting::$mobRegex,
            'country_code'  => "required_with:mobile_no|min:" . Setting::$countryCodeMin,
            'date_of_birth' => 'required',
            'start_date'    => 'required',
            'password'      => "required|min:" . Setting::$passwordMin . "|max:" . Setting::$passwordMax,
            'profile_image' => "nullable|image|mimes:" . Setting::$imageMimes,
        ];
    }
    public function messages()
    {
        return [
            'first_name.required'        => __('validation.required.text', ['attribute' => __('labels.first_name')]),
            'last_name.required'         => __('validation.required.text', ['attribute' => __('labels.last_name')]),
            'email.required'             => __('validation.required.text', ['attribute' => __('labels.email')]),
            'mobile_no.required'         => __('validation.required.text', ['attribute' => __('labels.mobile_no')]),
            'country_code.required_with' => __('validation.required_with', ['value' => __('labels.mobile_no')]),
            'country_code.min'           => __('validation.min.numeric', ['attribute' => __('labels.country_code')]),
            'date_of_birth.required'     => __('validation.required.text', ['attribute' => __('labels.date_of_birth')]),
            'start_date.required'        => __('validation.required.text', ['attribute' => __('labels.start_date')]),
            'password.required'          => __('validation.required.text', ['attribute' => __('labels.password')]),
            'password.min'               => __('validation.min.string', ['attribute' => __('labels.password')]),
            'password.max'               => __('validation.max.string', ['attribute' => __('labels.password')]),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = Setting::sendError(__('messages.failed_to_module_name', ['moduleName' => __('labels.user')]), $validator->errors(), 400);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
