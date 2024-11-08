<?php

namespace App\Http\Requests\CompantStackModule;

use App\Library\Setting;
use Illuminate\Foundation\Http\FormRequest;

class DuplicateStackRequest extends FormRequest
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
            'project_id'                => "required|exists:company_projects,id,deleted_at,NULL",
            'company_id'                => "required|exists:mst_company,id,deleted_at,NULL",
            'company_stack_modules_id'  => "required|exists:company_stack_modules,id,deleted_at,NULL",
        ];
    }

    public function messages()
    {
        return [
            'project_id.required'                => __('validation.required.text', ['attribute' => __('labels.project_name')]),
            'company_id.required'                => __('validation.required.text', ['attribute' => __('labels.project_name')]),
            'company_stack_modules_id.required'  => __('validation.required.text', ['attribute' => __('labels.project_name')]),
            'project_id.exists'                  => __('validation.exists', ['attribute' => __('labels.company')]),
            'company_id.exists'                  => __('validation.exists', ['attribute' => __('labels.company')]),
            'company_stack_modules_id.exists'    => __('validation.exists', ['attribute' => __('labels.company')]),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $id = $this->route()->parameter('id');
        if (empty($id)) {
            $response = Setting::sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.company_stack_category')]), $validator->errors(), 400);
        } else {
            $response = Setting::sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.company_stack_category')]), $validator->errors(), 400);
        }

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
