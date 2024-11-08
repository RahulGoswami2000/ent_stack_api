<?php

namespace App\Http\Requests\TeamMember;

use App\Library\Setting;
use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateRequest extends FormRequest
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
            'first_name'   => "required|min:" . Setting::$nameMin . "|max:" . Setting::$nameMax,
            'last_name'    => "required|min:" . Setting::$nameMin . "|max:" . Setting::$nameMax,
            'mobile_no'    => "required|min:" . Setting::$mobMin . "|max:" . Setting::$mobMax . "|regex:" . Setting::$mobRegex,
            'email'        => "required|email|unique:mst_users,email,$id,id,deleted_at,NULL|min:" . Setting::$emailMin . "|max:" . Setting::$emailMax,
            'job_role'     => "required|min:" . Setting::$myTeamNameMin . "|max:" . Setting::$nameMax,
            'password'     => empty($id) ? "required|min:" . Setting::$passwordMin . "|max:" . Setting::$passwordMax : "",
        ];
    }

    public function messages()
    {
        return [
            // 'profile_pic.required' => __('validation.required.text', ['attribute' => __('labels.profile_pic')]),
            // 'profile_pic.max'      => __('validation.max.file', ['attribute' => __('labels.profile_pic')]),
            'first_name.required'        => __('validation.required.text', ['attribute' => __('labels.first_name')]),
            'first_name.min'             => __('validation.min.string', ['attribute' => __('labels.first_name')]),
            'first_name.max'             => __('validation.max.string', ['attribute' => __('labels.first_name')]),
            'last_name.required'         => __('validation.required.text', ['attribute' => __('labels.last_name')]),
            'last_name.min'              => __('validation.min.string', ['attribute' => __('labels.last_name')]),
            'last_name.max'              => __('validation.max.string', ['attribute' => __('labels.last_name')]),
            'email.required'             => __('validation.required.text', ['attribute' => __('labels.email')]),
            'email.email'                => __('validation.email', ['attribute' => __('labels.email')]),
            'email.unique'               => __('validation.unique', ['attribute' => __('labels.email')]),
            'email.min'                  => __('validation.min.string', ['attribute' => __('labels.email')]),
            'email.max'                  => __('validation.max.string', ['attribute' => __('labels.email')]),
            'mobile_no.required'         => __('validation.required.text', ['attribute' => __('labels.mobile_no')]),
            'mobile_no.min'              => __('validation.min.string', ['attribute' => __('labels.mobile_no')]),
            'mobile_no.max'              => __('validation.max.string', ['attribute' => __('labels.mobile_no')]),
            'mobile_no.regex'            => __('validation.regex.other', ['attribute' => __('labels.mobile_no')]),
            'password.required'          => __('validation.required.text', ['attribute' => __('labels.password')]),
            'password.min'               => __('validation.min.string', ['attribute' => __('labels.password')]),
            'password.max'               => __('validation.max.string', ['attribute' => __('labels.password')]),
            'job_role.required'          => __('validation.required.text', ['attribute' => __('labels.job_role')]),
            'job_role.min'               => __('validation.min.string', ['attribute' => __('labels.job_role')]),
            'job_role.max'               => __('validation.max.string', ['attribute' => __('labels.job_role')]),
        ];
    }

    public function withValidator(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $id       = $this->route()->parameter('id') ?? null;
        $postData = $this->validationData();

        $user = \DB::table('mst_users')
            ->where('mst_users.mobile_no', $postData['mobile_no'])
            ->where('mst_users.id', '!=', $id)
            //            ->whereNull('mst_users.deleted_at')
            ->count();

        if (!empty($user)) {
            $validator->after(function ($validator) {
                $validator->errors()->add('mobile_no', __('validation.unique', ['attribute' => __('labels.mobile_no')]));
            });
        }
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $id = $this->route()->parameter('id');

        $errors = $validator->errors();
//        foreach ($validator->errors()->messages() as $key => $value) {
//            if ($key == 'mobile_no' || $key == 'country_code') {
//                $key = 'mobile_number';
//            }
//            $newValue     = is_array($value) ? $value : [$value];
//            $errors[$key] = !empty($errors[$key]) ? array_merge($errors[$key], $newValue) : $newValue;
//        }

        if (empty($id)) {
            $response = Setting::sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.team_member')]), $errors, 400);
        } else {
            $response = Setting::sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.team_member')]), $errors, 400);
        }

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
