<?php

namespace App\Http\Requests\ScorecardStack;

use App\Library\Setting;
use Illuminate\Foundation\Http\FormRequest;

class UpdateScorecardNodeDataRequest extends FormRequest
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
            'scorecard_node_data_id' => 'required|exists:scorecard_stack_node_data,id,deleted_at,NULL',
            'value'                  => 'required',
        ];
    }

    public function messages()
    {
        return [
            'scorecard_node_data_id.required' => __('validation.required.text', ['attribute' => __('labels.scorecard_data')]),
            'scorecard_node_data_id.exists'   => __('validation.exists', ['attribute' => __('labels.scorecard_data')]),
            'value.required'                  => __('validation.required.text', ['attribute' => __('labels.value')]),
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