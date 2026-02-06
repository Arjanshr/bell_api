<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'image' => 'required|image',
            'image_alt' => 'nullable|string|max:255',
            'status' => 'required|in:publish,unpublish',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];

        if ($this->isMethod('patch') || $this->isMethod('put')) {
            $rules['slug'] = 'required|string|max:255|unique:blogs,slug,' . $this->route('blog')->id;
            $rules['image'] = 'nullable|image';
        }

        return $rules;
    }

    protected function passedValidation()
    {
        if ($this->hasFile('image')) {
            $image_name = rand(0, 99999) . time() . '.' . $this->image->extension();
            $this->image->move(storage_path('app/public/blogs'), $image_name);
            $this['image']->file_name = $image_name;
        }
    }
}
