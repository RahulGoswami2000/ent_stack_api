<?php

namespace App\Http\Requests\MetricGroup;

use App\Library\Setting;
use Illuminate\Foundation\Http\FormRequest;

class AddRemoveMetricRequest extends FormRequest
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
            'metric_id'       => 'exists:metric,id',
            'metric_group_id' => 'exists:metric_groups,id',
        ];
    }

    public function messages()
    {
        return [
            'metric_id.exists'       => __('validation.exists', ['attribute' => __('labels.metric')]),
            'metric_group_id.exists' => __('validation.exists', ['attribute' => __('labels.metric_group')]),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $id = $this->route()->parameter('id');
        if (empty($id)) {
            $response = Setting::sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.metric')]), $validator->errors(), 400);
        } else {
            $response = Setting::sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.metric')]), $validator->errors(), 400);
        }

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
