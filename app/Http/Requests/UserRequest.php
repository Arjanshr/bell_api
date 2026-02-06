<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
    public function rules()
    {
        // Get the ID of the user being edited (if any)
        $userId = $this->route('user') ? $this->route('user')->id : null;
    
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
                'required_without_all:phone',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:15',
                Rule::unique('users', 'phone')->ignore($userId),
                'required_without_all:email',
            ],
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:500',
    
            // Require role only if user ID is null (registration)
            // 'role' => $userId ? 'sometimes|array' : 'required|array',
            'role.*' => ['sometimes', Rule::exists('roles', 'name')],
        ];
    }
}
