<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryBrandRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if ($this->isMethod('post')) {
            return [
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'required|exists:brands,id',
                'summary' => 'nullable|string',
                'description' => 'nullable|string',
                'meta_title' => 'nullable|string',
                'meta_description' => 'nullable|string',
            ];
        }

        // For update (edit)
        return [
            'summary' => 'nullable|string',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
        ];
    }
}
