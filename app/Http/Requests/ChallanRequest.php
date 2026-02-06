<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChallanRequest extends FormRequest
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
            'challan_date'=>['required'],
            'from_warehouse_id'=>['required'],
            'to_warehouse_id'=>['required'],
            'items'=>['required','array'],
            'quantity'=>['required'],
        ];
    }
}
