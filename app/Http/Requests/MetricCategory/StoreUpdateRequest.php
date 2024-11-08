<?php

namespace App\Http\Requests\MetricCategory;

use App\Library\Setting;
use Illuminate\Contracts\Validation\Validator;
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
            'name'        => "required|min:" . Setting::$nameMin . "|max:" . Setting::$nameMax . "|unique:metric_categories,name,$id,id,deleted_at,NULL",
            'metric_id'   => 'array',
            'metric_id.*' => 'exists:metric,id,metric_category_id,NULL,deleted_at,NULL',
        ];
    }

    public function messages()
    {
        return [
            'name.required'      => __('validation.required.text', ['attribute' => __('labels.metric_category_name')]),
            'name.min'           => __('validation.min.string', ['attribute' => __('labels.metric_category_name')]),
            'name.max'           => __('validation.max.string', ['attribute' => __('labels.metric_category_name')]),
            'name.unique'        => __('validation.unique', ['attribute' => __('labels.metric_category_name')]),
            'metric_id.array'    => __('validation.array', ['attribute' => __('labels.metric')]),
            'metric_id.*.exists' => __('validation.exists', ['attribute' => __('labels.metric')]),
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
