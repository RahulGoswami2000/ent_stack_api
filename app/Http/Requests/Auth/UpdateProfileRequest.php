<?php

namespace App\Http\Requests\Auth;

use App\Library\Setting;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
        $id = $this->route()->parameter('id') ?? null;
        return [
            'first_name'    => "required|min:" . Setting::$nameMin . "|max:" . Setting::$nameMax,
            'last_name'     => "required|min:" . Setting::$nameMin . "|max:" . Setting::$nameMax,
            'job_role'      => "nullable|min:" . Setting::$myTeamNameMin . "|max:" . Setting::$nameMax,
            'date_of_birth' => 'nullable|date_format:Y-m-d',
            'start_date'    => 'nullable|date_format:Y-m-d',
            'email'         => "required|email|unique:mst_users,email,$id,id,deleted_at,NULL|min:" . Setting::$emailMin . "|max:" . Setting::$emailMax,
        ];
    }

    public function messages()
    {
        return [
            'first_name.required'       => __('validation.required.text', ['attribute' => __('labels.first_name')]),
            'last_name.required'        => __('validation.required.text', ['attribute' => __('labels.last_name')]),
            'job_role.min'              => __('validation.min.string', ['attribute' => __('labels.job_role')]),
            'job_role.max'              => __('validation.max.string', ['attribute' => __('labels.job_role')]),
            'date_of_birth.date_format' => __('validation.date_format', ['attribute' => __('labels.date_of_birth'), 'date_format' => __('labels.date_format')]),
            'start_date.date_format'    => __('validation.date_format', ['attribute' => __('labels.start_date'), 'date_format' => __('labels.date_format')]),
            'email.required'            => __('validation.required.text', ['attribute' => __('labels.email')]),
            'email.email'               => __('validation.email', ['attribute' => __('labels.email')]),
            'email.unique'              => __('validation.unique', ['attribute' => __('labels.email')]),
            'email.min'                 => __('validation.min.string', ['attribute' => __('labels.email')]),
            'email.max'                 => __('validation.max.string', ['attribute' => __('labels.email')]),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = Setting::sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.profile')]), $validator->errors(), 400);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
