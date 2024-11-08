<?php

namespace App\Http\Requests\CompanyProject;

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
            'company_id' => "required|integer|exists:mst_company,id,deleted_at,NULL",
            'name'       => "required|min:" . Setting::$nameMin . "|max:" . Setting::$projectNameMax,
        ];
    }

    public function messages()
    {
        return [
            'company_id.required' => __('validation.required.text', ['attribute' => __('labels.company')]),
            'company_id.exists'   => __('validation.exists', ['attribute' => __('labels.company')]),
            'name.required'       => __('validation.required.text', ['attribute' => __('labels.project_name')]),
            'name.min'            => __('validation.min.string', ['attribute' => __('labels.project_name')]),
            'name.max'            => __('validation.max.string', ['attribute' => __('labels.project_name')]),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $id = $this->route()->parameter('id');
        if (empty($id)) {
            $response = Setting::sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.company_project')]), $validator->errors(), 400);
        } else {
            $response = Setting::sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.company_project')]), $validator->errors(), 400);
        }

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
