<?php

namespace App\Http\Requests\WebTeamMember;

use App\Library\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

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
    public function rules(Request $request)
    {
        return [
            'company_id'                       => 'required|exists:mst_company,id,deleted_at,NULL',
            'job_role'                         => "required|min:" . Setting::$myTeamNameMin . "|max:" . Setting::$nameMax,
            'role_id'                          => 'required|exists:mst_roles,id,deleted_at,NULL',
            'data.*.company_stack_modules_id'  => 'required|exists:company_stack_modules,id,deleted_at,NULL',
            'data.*.company_stack_category_id' => 'required|exists:company_stack_category,id,deleted_at,NULL',
            'data.*.project_id'                => 'required|exists:company_projects,id,deleted_at,NULL',
        ];
    }

    public function messages()
    {
        return [
            'company_id.exists'                         => __('validation.exists', ['attribute' => __('labels.company')]),
            'job_role.required'                         => __('validation.required.text', ['attribute' => __('labels.job_role')]),
            'job_role.min'                              => __('validation.min.string', ['attribute' => __('labels.job_role')]),
            'job_role.max'                              => __('validation.max.string', ['attribute' => __('labels.job_role')]),
            'user_id'                                   => __('validation.exists', ['attribute' => __('labels.company')]),
            'role_id.exists'                            => __('validation.exists', ['attribute' => __('labels.role')]),
            'data.*.company_stack_category_id.exists'   => __('validation.exists', ['attribute' => __('labels.company_stack_category')]),
            'data.*.company_stack_modules_id.exists'    => __('validation.exists', ['attribute' => __('labels.company_stack_module')]),
            'data.*.project_id.exists'                  => __('validation.exists', ['attribute' => __('labels.project')]),
            'data.*.company_stack_modules_id.required'  => __('validation.exists', ['attribute' => __('labels.company_stack_module')]),
            'data.*.company_stack_category_id.required' => __('validation.exists', ['attribute' => __('labels.company_stack_category')]),
            'data.*.project_id.required'                => __('validation.exists', ['attribute' => __('labels.project')]),
        ];
    }

    public function withValidator(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $id       = $this->route()->parameter('id') ?? null;
        $postData = $this->validationData();  // Start with
        if (!empty($postData['email']) && empty($id)) {
            $user = \DB::table('mst_users')
                ->leftjoin('mst_user_company_matrix', 'mst_user_company_matrix.user_id', '=', 'mst_users.id')
                ->select('mst_users.id')
                ->where('mst_users.email', $postData['email'])
                ->where('mst_user_company_matrix.company_id', $postData['company_id'])
                ->whereNull('mst_user_company_matrix.deleted_at')
                ->first();

            if (!empty($user)) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('email', __('validation.user_associated_already', ['attribute' => __('labels.user')]));
                });
            }
        }

        if (!empty($postData['role_id']) && empty($id)) {
            $data = \DB::table('mst_roles')
                ->select('mst_roles.id')
                ->where('mst_roles.id', $postData['role_id'])
                ->where('mst_roles.name', 'Owner')->first();
        }
        if (!empty($data)) {
            $validator->after(function ($validator) {
                $validator->errors()->add('role_id', __('validation.already_a_owner'));
            });
        }
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $id = $this->route()->parameter('id');
        if (empty($id)) {
            $response = Setting::sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.user')]), $validator->errors(), 400);
        } else {
            $response = Setting::sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.user')]), $validator->errors(), 400);
        }

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
