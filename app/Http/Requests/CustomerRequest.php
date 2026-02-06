<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'=>['required'],
            'email'=>['required','email'],
            'location'=>['required','string'],
            'phone'=>['required'],
            'credit'=>['nullable','numeric','min:0'],
            'debit'=>['nullable','numeric','min:0'],
            'credit_limit'=>['nullable','numeric','min:0'],
            'vat_no'=>['nullable','numeric','digits:13'],
        ];
    }
}
