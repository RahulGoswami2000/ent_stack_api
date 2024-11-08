<?php

namespace App\Http\Requests\TeamStack;

use App\Library\Setting;
use Illuminate\Foundation\Http\FormRequest;

class AssignStackRequest extends FormRequest
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
            'user_id'                   => "array|nullable|exists:mst_users,id,deleted_at,NULL",
            'company_id'                => "required|exists:mst_company,id,deleted_at,NULL",
            'project_id'                => "required|exists:company_projects,id,deleted_at,NULL",
            'company_stack_modules_id'  => "required|exists:company_stack_modules,id,deleted_at,NULL",
            'company_stack_category_id' => "required|exists:company_stack_category,id,deleted_at,NULL",
        ];
    }

    public function messages()
    {
        return [
            'user_id.exists'                     => __('validation.exists', ['attribute' => __('labels.user')]),
            'company_id.required'                => __('validation.required.text', ['attribute' => __('labels.company')]),
            'company_id.exists'                  => __('validation.exists', ['attribute' => __('labels.company')]),
            'project_id.required'                => __('validation.required.text', ['attribute' => __('labels.project')]),
            'project_id.exists'                  => __('validation.exists', ['attribute' => __('labels.project')]),
            'company_stack_modules_id.required'  => __('validation.required.text', ['attribute' => __('labels.company_stack_module')]),
            'company_stack_modules_id.exists'    => __('validation.exists', ['attribute' => __('labels.company_stack_module')]),
            'company_stack_category_id.required' => __('validation.required.text', ['attribute' => __('labels.company_stack_category')]),
            'company_stack_category_id.exists'   => __('validation.exists', ['attribute' => __('labels.company_stack_category')]),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $id = $this->route()->parameter('id');
        if (empty($id)) {
            $response = Setting::sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.assign_stack')]), $validator->errors(), 400);
        } else {
            $response = Setting::sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.assign_stack')]), $validator->errors(), 400);
        }

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
