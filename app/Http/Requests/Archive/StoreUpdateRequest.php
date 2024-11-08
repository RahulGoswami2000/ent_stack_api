<?php

namespace App\Http\Requests\Archive;

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
        return [
            'type'                      => "required|in:1,2",
            'company_id'                => "required|integer|exists:mst_company,id,deleted_at,NULL",
            'project_id'                => "required|integer|exists:company_projects,id,deleted_at,NULL",
            'company_stack_modules_id'  => "required|integer|exists:company_stack_modules,id,deleted_at,NULL",
            'company_stack_category_id' => "required|integer|exists:company_stack_category,id,deleted_at,NULL",
            'scorecard_stack_id'        => "required|integer|exists:scorecard_stack,id,deleted_at,NULL",
        ];
    }

    public function messages()
    {
        return [
            'type.required'                      => __('validation.required.text', ['attribute' => __('labels.archive_type')]),
            'type.in'                            => __('validation.in', ['attribute' => __('labels.archive_type')]),
            'company_id.required'                => __('validation.required.text', ['attribute' => __('labels.company_id')]),
            'company_id.exists'                  => __('validation.exists', ['attribute' => __('labels.company')]),
            'project_id.exists'                  => __('validation.exists', ['attribute' => __('labels.project')]),
            'project_id.required'                => __('validation.required.text', ['attribute' => __('labels.project_id')]),
            'company_stack_modules_id.required'  => __('validation.required.text', ['attribute' => __('labels.company_stack_module')]),
            'company_stack_modules_id.exists'    => __('validation.exists', ['attribute' => __('labels.company_stack_module')]),
            'company_stack_category_id.required' => __('validation.required.text', ['attribute' => __('labels.company_stack_category')]),
            'company_stack_category_id.exists'   => __('validation.exists', ['attribute' => __('labels.company_stack_category')]),
            'scorecard_stack_id.required'        => __('validation.required.text', ['attribute' => __('labels.scorecard_stack')]),
            'scorecard_stack_id.exists'          => __('validation.exists', ['attribute' => __('labels.scorecard_stack')]),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $id = $this->route()->parameter('id');
        if (empty($id)) {
            $response = Setting::sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.scorecard_stack')]), $validator->errors(), 400);
        } else {
            $response = Setting::sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.scorecard_stack')]), $validator->errors(), 400);
        }

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
