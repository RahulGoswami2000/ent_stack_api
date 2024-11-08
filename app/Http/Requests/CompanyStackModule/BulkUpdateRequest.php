<?php

namespace App\Http\Requests\CompanyStackModule;

use App\Library\Setting;
use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateRequest extends FormRequest
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
            'data.*.is_deleted'               => 'required|in:0,1',
            'data.*.name'                     => "required|min:" . Setting::$myTeamNameMin . "|max:" . Setting::$nameMax,
            'data.*.company_stack_modules_id' => "required|exists:company_stack_modules,id,deleted_at,NULL",
        ];
    }

    public function messages()
    {
        return [
            'data.*.is_deleted.required'               => __('validation.required.text', ['attribute' => __('labels.is_deleted')]),
            'data.*.is_deleted.in'                     => __('validation.in', ['attribute' => __('labels.is_deleted')]),
            'data.*.name.required'                     => __('validation.required.text', ['attribute' => __('labels.stack_name')]),
            'data.*.name.min'                          => __('validation.min.string', ['attribute' => __('labels.stack_name')]),
            'data.*.name.max'                          => __('validation.max.string', ['attribute' => __('labels.stack_name')]),
            'data.*.company_stack_modules_id.required' => __('validation.required.text', ['attribute' => __('labels.company_stack_module')]),
            'data.*.company_stack_modules_id.exists'   => __('validation.exists', ['attribute' => __('labels.company_stack_module')])
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = Setting::sendError(__('messages.failed_to_retrieve_module_name', ['moduleName' => __('labels.user')]), $validator->errors(), 400);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
