<?php

namespace App\Http\Requests;

use App\Models\Slider;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SliderRequest extends FormRequest
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
            'type'=>['required','in:slider,banner'],
            'title'=>['nullable','string'],
            'link_url'=>['nullable','url'],
            'display_order'=>['required','numeric','min:1',],
            'image'=>['nullable','mimes:webp,jpeg,png,jpg,gif,svg,ico,pdf','max:20480'],
            'mobile_image'=>['nullable','mimes:jpeg,png,jpg,gif,svg,ico,pdf','max:20480'],
        ];
    }

    protected function passedValidation()
    {
        if ($this->hasFile('image')) {
            $image_name = rand(0, 99999) . time() . '.' . $this->image->extension();
            $this->image->move(storage_path('app/public/sliders'), $image_name);
            $this['image']->file_name = $image_name;
        }
        if ($this->hasFile('mobile_image')) {
            $image_name = rand(0, 99999) . time() . '.' . $this->mobile_image->extension();
            $this->mobile_image->move(storage_path('app/public/sliders'), $image_name);
            $this['mobile_image']->file_name = $image_name;
        }
    }
}
