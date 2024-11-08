<?php

namespace App\Http\Requests\Roles;

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
            'roles_name' => "required|unique:mst_roles,name,$id,id,deleted_at,NULL|min:" . Setting::$nameMin . "|max:" . Setting::$nameMax,
            'role_type'  => "required|in:1,2,"
        ];
    }

    public function messages()
    {
        return [
            'roles_name.required' => __('validation.required.text', ['attribute' => __('labels.role_name')]),
            'roles_name.min'      => __('validation.min.string', ['attribute' => __('labels.role_name')]),
            'roles_name.max'      => __('validation.max.string', ['attribute' => __('labels.role_name')]),
            'role_type.required'  => __('validation.required.text', ['attribute' => __('labels.roles_type')]),
            'role_type.in'        => __('validation.in', ['attribute' => __('labels.roles_type')]),
            'roles_name.unique'   => __('validation.unique', ['attribute' => __('labels.role_name')]),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $id = $this->route()->parameter('id');
        if (empty($id)) {
            $response = Setting::sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.role')]), $validator->errors(), 400);
        } else {
            $response = Setting::sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.role')]), $validator->errors(), 400);
        }

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
