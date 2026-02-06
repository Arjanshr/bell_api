<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends BaseController
{
    public function sliders()
    {
        $sliders =  Slider::where('type', 'slider')->orderBy('display_order')->get();
        return $this->sendResponse(SliderResource::collection($sliders), 'Sliders retrieved successfully.');
    }
    public function banners()
    {
        $sliders =  Slider::where('type', 'banner')->orderBy('display_order')->get();
        return $this->sendResponse(SliderResource::collection($sliders), 'Banner retrieved successfully.');
    }
}
