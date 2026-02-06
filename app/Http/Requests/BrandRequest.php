<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
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
        $rules = [
            'name' => 'required|string|max:255',
            'summary' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'meta_title' => 'nullable', 'string', 'max:255',
            'meta_description' => 'nullable', 'string',

        ];

        // Add the required validation for image only during creation
        if ($this->isMethod('post')) {
            $rules['image'] = 'required|image|mimes:webp,jpeg,png,jpg,gif|max:2048'; // Add your preferred rules for the image
        } else {
            $rules['image'] = 'nullable|image|mimes:webp,jpeg,png,jpg,gif|max:2048'; // Make image optional during update
        }

        return $rules;
    }

    protected function passedValidation()
    {
        if ($this->hasFile('image')) {
            $image_name = rand(0, 99999) . time() . '.' . $this->image->extension();
            $this->image->move(storage_path('app/public/brands'), $image_name);
            $this['image']->file_name = $image_name;
        }
    }
}
