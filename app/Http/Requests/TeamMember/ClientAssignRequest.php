<?php

namespace App\Http\Requests\TeamMember;

use App\Library\Setting;
use Illuminate\Foundation\Http\FormRequest;

class ClientAssignRequest extends FormRequest
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
            'user_id'       => 'required|integer',
            'client_assign' => 'required|in:0,1',
            'company_id'    => 'array',
            'company_id.*'  => 'exists:mst_company,id,deleted_at,NULL',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required'       => __('validation.required.text', ['attribute' => __('labels.user_id')]),
            'client_assign.required' => __('validation.required.text', ['attribute' => __('labels.client_assign')]),
            'client_assign.in'       => __('validation.in', ['attribute' => __('labels.client_assign')]),
            'company_id.array'       => __('validation.array', ['attribute' => __('labels.company')]),
            'company_id.*.exists'    => __('validation.exists', ['attribute' => __('labels.company')]),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $id = $this->route()->parameter('id');
        if (empty($id)) {
            $response = Setting::sendError(__('messages.client_assigned_successfully'), $validator->errors(), 400);
        } else {
            $response = Setting::sendError(__('messages.failed_to_assign_client'), $validator->errors(), 400);
        }

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
