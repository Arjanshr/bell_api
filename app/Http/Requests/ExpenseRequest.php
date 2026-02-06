<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'amount'=>['required','numeric'],
            'date'=>['required','date'],
            'description'=>['nullable','string'],
            'user_id'=>['nullable','numeric'],
            'category_id'=>['required','numeric'],
            'attachment'=>['required','mimes:jpeg,png,jpg,gif,svg,ico,pdf','max:2048'],
        ];
    }

    protected function passedValidation()
    {
        if ($this->hasFile('attachment')) {
            $image_name = rand(0, 99999) . time() . '.' . $this->attachment->extension();
            $this->attachment->move(storage_path('app/public/expenses'), $image_name);
            $this['attachment']->file_name = $image_name;
        }
    }

}
