<?php

namespace App\Http\Requests;

use App\Enums\CampaignStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;


class CampaignRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('campaigns', 'name')->ignore($this->campaign),
            ],
            'start_date' => ['required', 'date', 'before:end_date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', new Enum(CampaignStatus::class)],
            'background_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'], // Max 2MB
            'theme_color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'], // Must be a valid hex color
            'has_active_period' => ['nullable', 'boolean'],
            'start_time' => ['required_if:has_active_period,1', 'nullable', 'date_format:H:i'],
            'end_time' => ['required_if:has_active_period,1', 'nullable', 'date_format:H:i', 'after:start_time'],
            'url' => ['nullable', 'url'], // Added URL validation
            'description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'], // added
            'meta_description' => ['nullable', 'string', 'max:500'], // added
            'min_cart_value' => 'nullable|numeric|min:0|required_if:type,free_delivery',
            'type' => ['required', Rule::in(['free_delivery', 'discount', 'banner', 'offers'])],
            'campaign_banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5140'], // Max 2MB
        ];
        if ($this->isMethod('patch') || $this->isMethod('put')) {
            $campaign = $this->route('campaign'); // Make sure your route parameter is 'campaign'
            $rules['slug'] = 'nullable|string|max:255|unique:campaigns,slug,' . ($campaign ? $campaign->id : '');
        }
        return $rules;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'has_active_period' => $this->has('has_active_period'),
        ]);
    }
}
