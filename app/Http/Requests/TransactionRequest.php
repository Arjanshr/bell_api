<?php

namespace App\Http\Requests;

use App\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class TransactionRequest extends FormRequest
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
            'transaction_date' => ['required', 'date'],
            'product_id'=>['required','numeric'],
            'quantity'=>['nullable','numeric'],
            'lc_number'=>['nullable','string'],
            'rate'=>['nullable','numeric'],
            'interest_rate'=>['nullable','numeric'],
            'grace_period'=>['nullable','numeric'],
            'lead_id'=>['nullable','numeric'],
            'user_id'=>['nullable','numeric'],
            'invoice_number'=>['nullable','string',Rule::unique('transactions')->ignore($this->transaction)],
            'type'=>['required',new Enum(TransactionType::class)],
        ];
    }

}
