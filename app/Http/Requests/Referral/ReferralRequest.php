<?php

namespace App\Http\Requests\Referral;

use App\Library\Setting;
use Illuminate\Foundation\Http\FormRequest;

class ReferralRequest extends FormRequest
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
            'id'          => 'required|exists:refer_client,id,deleted_at,NULL',
            'is_referred' => 'required|integer|in:' . join(',', array_column(config('global.REFERRAL_STATUS'), 'id')),
        ];
    }

    public function messages()
    {
        return [
            'id.required'          => __('validation.required.text', ['attribute' => __('labels.refer_client')]),
            'id.exists'            => __('validation.exists', ['attribute' => __('labels.refer_client')]),
            'is_referred.required' => __('validation.required.text', ['attribute' => __('labels.referral')]),
            'is_referred.in'       => __('validation.in', ['attribute' => __('labels.status')]),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $id = $this->route()->parameter('id');
        if (empty($id)) {
            $response = Setting::sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.client_assign')]), $validator->errors(), 400);
        } else {
            $response = Setting::sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.client_assign')]), $validator->errors(), 400);
        }

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
