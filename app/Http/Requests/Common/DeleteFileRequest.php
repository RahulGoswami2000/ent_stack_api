<?php

namespace App\Http\Requests\Common;

use App\Library\Setting;
use Illuminate\Foundation\Http\FormRequest;

class DeleteFileRequest extends FormRequest
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
            'file_url' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'file_url.required' => __('validation.required.select', ['attribute' => __('labels.file')]),
            'file_url.url'    => __('validation.mimes', ['attribute' => __('labels.file')]),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = Setting::sendError(__('messages.failed_to_delete_file'), $validator->errors(), 400);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
