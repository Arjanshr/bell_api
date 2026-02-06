<?php

namespace App\Http\Requests;

use App\Enums\CategoryStatus;
use App\Enums\CategoryType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class CategoryRequest extends FormRequest
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
            'summary' => ['nullable', 'string', 'max:300'],
            'parent_id'=>['nullable','numeric'],
            'image'=>['nullable','mimes:webp,jpeg,png,jpg,gif,svg,ico,pdf','max:2048'],
            'status'=>['required', new Enum(CategoryStatus::class)],
            'description'=>['nullable','string'],
            'meta_title' => ['nullable', 'string', 'max:255'], // added
            'meta_description' => ['nullable', 'string', 'max:500'], // added
        ];
    }

    protected function passedValidation()
    {
        if ($this->hasFile('image')) {
            $image_name = rand(0, 99999) . time() . '.' . $this->image->extension();
            $this->image->move(storage_path('app/public/categories'), $image_name);
            $this['image']->file_name = $image_name;
        }
    }
}
