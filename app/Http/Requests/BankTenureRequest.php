<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankTenureRequest extends FormRequest
{
    public function authorize()
    {
        return true; // adjust as needed for permissions
    }

    public function rules()
    {
        return [
            'bank_id' => 'required|exists:banks,id',
            'months' => 'required|integer|min:1',
            'service_charge_percent' => 'required|numeric|min:0',
            'min_service_charge_amount' => 'nullable|numeric|min:0',
        ];
    }
}
