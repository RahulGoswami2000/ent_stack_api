<?php

namespace App\Http\Requests\Subscription;

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
            'name'        => "required|unique:subscriptions,name,$id,id,deleted_at,NULL|min:" . Setting::$nameMin . "|max:" . Setting::$nameMax,
            'description' => "min:" . Setting::$descriptionMin . "|max:" . Setting::$descriptionMax,
            'amount'      => "required|numeric|min:1"
        ];
    }

    public function messages()
    {
        return [
            'name.required'        => __('validation.required.text', ['attribute' => __('labels.subscription_name')]),
            'name.min'             => __('validation.min.string', ['attribute' => __('labels.subscription_name')]),
            'name.max'             => __('validation.max.string', ['attribute' => __('labels.subscription_name')]),
            'name.unique'          => __('validation.unique', ['attribute' => __('labels.subscription_name')]),
            'description.required' => __('validation.required.text', ['attribute' => __('labels.subscription_description')]),
            'description.min'      => __('validation.min.string', ['attribute' => __('labels.subscription_description')]),
            'description.max'      => __('validation.max.string', ['attribute' => __('labels.subscription_description')]),
            'amount.required'      => __('validation.required.text', ['attribute' => __('labels.subscription_amount')]),
            'amount.min'           => __('validation.min.numeric', ['attribute' => __('labels.subscription_amount')]),
            'amount.numeric'       => __('validation.numeric', ['attribute' => __('labels.subscription_amount')]),
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
