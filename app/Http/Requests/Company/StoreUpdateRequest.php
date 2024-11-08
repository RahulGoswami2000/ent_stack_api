<?php

namespace App\Http\Requests\Company;

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
        return [
            'company_name' => "required|min:" . Setting::$nameMin . "|max:" . Setting::$companyNameMax,
            'website_url'  => 'url',
        ];
    }

    public function messages()
    {
        return [
            'company_name.required' => __('validation.required.text', ['attribute' => __('labels.company_name')]),
            'company_name.min'      => __('validation.min.string', ['attribute' => __('labels.company_name')]),
            'company_name.max'      => __('validation.max.string', ['attribute' => __('labels.company_name')]),
            'website_url.url'       => __('validation.url'),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $id = $this->route()->parameter('id');
        if (empty($id)) {
            $response = Setting::sendError(__('messages.failed_to_save_module_name', ['moduleName' => __('labels.organization')]), $validator->errors(), 400);
        } else {
            $response = Setting::sendError(__('messages.failed_to_update_module_name', ['moduleName' => __('labels.organization')]), $validator->errors(), 400);
        }

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
