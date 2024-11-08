<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
            // 'user_id'         => 'required',
            // 'subscription_id' => 'required',
            // 'amount'          => 'required',
        ];
    }

    public function messages()
    {
        return [
            // 'user_id' => __('validation.required.text')
        ];
    }
}
