<?php

namespace App\Http\Requests\Common;

use App\Library\Setting;
use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
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
            'upload_file' => 'required|mimes: ' . Setting::$imageMimes,
        ];
    }

    public function messages()
    {
        return [
            'upload_file.required' => __('validation.required.select', ['attribute' => __('labels.file')]),
            'upload_file.mimes'    => __('validation.mimes', ['attribute' => __('labels.file')]),
            'upload_file.max'      => __('validation.max', ['attribute' => __('labels.file')]),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = Setting::sendError(__('messages.failed_to_upload_file'), $validator->errors(), 400);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
