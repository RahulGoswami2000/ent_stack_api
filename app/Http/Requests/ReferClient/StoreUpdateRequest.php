<?php

namespace App\Http\Requests\ReferClient;

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
            'first_name' => "required|min:" . Setting::$nameMin . "|max:" . Setting::$nameMax,
            'last_name'  => "required|min:" . Setting::$nameMin . "|max:" . Setting::$nameMax,
            'email'      => "required|email|min: " . Setting::$emailMin . "|max:" . Setting::$emailMax . "|unique:mst_users,email,$id,id,deleted_at,NULL|unique:refer_client,email,$id,id,deleted_at,NULL",
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => __('validation.required.text', ['attribute' => __('labels.first_name')]),
            'last_name.required'  => __('validation.required.text', ['attribute' => __('labels.last_name')]),
            'first_name.min'      => __('validation.min.string', ['attribute' => __('labels.first_name')]),
            'first_name.max'      => __('validation.max.string', ['attribute' => __('labels.first_name')]),
            'last_name.min'       => __('validation.min.string', ['attribute' => __('labels.last_name')]),
            'last_name.max'       => __('validation.max.string', ['attribute' => __('labels.last_name')]),
            'email.required'      => __('validation.required.text', ['attribute' => __('labels.email')]),
            'email.min'           => __('validation.min.string', ['attribute' => __('labels.email')]),
            'email.max'           => __('validation.max.string', ['attribute' => __('labels.email')]),
            'email.unique'        => __('validation.unique', ['attribute' => __('labels.email')]),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $id = $this->route()->parameter('id');
        if (empty($id)) {
            $response = Setting::sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.refer_client')]), $validator->errors(), 400);
        } else {
            $response = Setting::sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.refer_client')]), $validator->errors(), 400);
        }

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
