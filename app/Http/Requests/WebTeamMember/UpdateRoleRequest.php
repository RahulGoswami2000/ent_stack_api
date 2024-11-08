<?php

namespace App\Http\Requests\WebTeamMember;

use App\Library\Setting;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
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
            'role_id' => 'exists:mst_roles,id,deleted_at,NULL',
        ];
    }

    public function messages()
    {
        return [
            'role_id.exists' => __('validation.exists', ['attribute' => __('labels.role')]),
        ];
    }

    public function withValidator(Validator $validator)
    {
        $id = $this->route()->parameter('id') ?? null;
        $postData = $this->validationData();
        if (!empty($postData['role_id'])) {
            $data = \DB::table('mst_roles')
                ->select('mst_roles.id')
                ->where('mst_roles.id', $postData['role_id'])
                ->where('mst_roles.name', 'Owner')->first();
        }
        if (!empty($data) && empty($id)) {
            $validator->after(function ($validator) {
                $validator->errors()->add('role_id', __('validation.already_a_owner'));
            });
        }
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $id = $this->route()->parameter('id');
        if (empty($id)) {
            $response = Setting::sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.role')]), $validator->errors(), 400);
        } else {
            $response = Setting::sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.role')]), $validator->errors(), 400);
        }

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
