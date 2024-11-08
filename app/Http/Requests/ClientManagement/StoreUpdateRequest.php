<?php

namespace App\Http\Requests\ClientManagement;

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
            'email'        => "required|email|min:" . Setting::$emailMin . "|max:" . Setting::$emailMax . "|unique:mst_users,email,$id,id,deleted_at,NULL",
            'mobile_no'    => "required|min:" . Setting::$mobMin . "|max:" . Setting::$mobMax . "|regex:" . Setting::$mobRegex,
            'id'           => 'exists:mst_company,id,deleted_at,NULL',
        ];
    }

    public function messages()
    {
        return [
            'first_name.required'        => __('validation.required.text', ['attribute' => __('labels.first_name')]),
            'last_name.required'         => __('validation.required.text', ['attribute' => __('labels.last_name')]),
            'first_name.min'             => __('validation.min.numeric', ['attribute' => __('labels.first_name')]),
            'first_name.max'             => __('validation.max.numeric', ['attribute' => __('labels.first_name')]),
            'last_name.min'              => __('validation.min.numeric', ['attribute' => __('labels.last_name')]),
            'last_name.max'              => __('validation.max.numeric', ['attribute' => __('labels.last_name')]),
            'email.required'             => __('validation.required.text', ['attribute' => __('labels.email')]),
            'email.unique'               => __('validation.unique', ['attribute' => __('labels.email')]),
            'email.min'                  => __('validation.min.numeric', ['attribute' => __('labels.email')]),
            'email.max'                  => __('validation.max.numeric', ['attribute' => __('labels.email')]),
            'mobile_no.required'         => __('validation.required.text', ['attribute' => __('labels.mobile_no')]),
            'mobile_no.min'              => __('validation.min.numeric', ['attribute' => __('labels.mobile_no')]),
            'mobile_no.max'              => __('validation.max.numeric', ['attribute' => __('labels.mobile_no')]),
            'id.exists'                  => __('validation.exists', ['attribute' => __('labels.user')]),
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
            $response = Setting::sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.metric')]), $errors, 400);
        } else {
            $response = Setting::sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.metric')]), $errors, 400);
        }

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
