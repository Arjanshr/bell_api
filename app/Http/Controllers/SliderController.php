<?php

namespace App\Http\Controllers;

use App\Http\Requests\SliderRequest;
use App\Models\Slider;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::orderBy('display_order')->paginate(100);
        return view('admin.slider.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.slider.form');
    }

    public function insert(SliderRequest $request)
    {
        $slider = $request->validated();

        $slider['image'] = $request->hasFile('image') 
            ? $request->validated()['image']->file_name 
            : '';

        $slider['mobile_image'] = $request->hasFile('mobile_image') 
            ? $request->validated()['mobile_image']->file_name 
            : null;

        Slider::create($slider);
        toastr()->success('Slider Created Successfully!');
        return redirect()->route('sliders');
    }

    public function show(Slider $slider)
    {
        return view('admin.slider.show', compact('slider'));
    }

    public function edit(Slider $slider)
    {
        return view('admin.slider.form', compact('slider'));
    }

    public function update(Slider $slider, SliderRequest $request)
    {
        $slider->type = $request->type;
        $slider->title = $request->title;
        $slider->link_url = $request->link_url;
        $slider->display_order = $request->display_order;

        if ($request->hasFile('image')) {
            if (File::exists(storage_path("app/public/sliders/$slider->image"))) {
                File::delete(storage_path("app/public/sliders/$slider->image"));
            }
            $slider->image = $request->validated()['image']->file_name;
        }

        if ($request->hasFile('mobile_image')) {
            if ($slider->mobile_image && File::exists(storage_path("app/public/sliders/$slider->mobile_image"))) {
                File::delete(storage_path("app/public/sliders/$slider->mobile_image"));
            }
            $slider->mobile_image = $request->validated()['mobile_image']->file_name;
        }

        $slider->save();
        toastr()->success('Slider Edited Successfully!');
        return redirect()->route('sliders');
    }

    public function delete(Slider $slider)
    {
        if (File::exists(storage_path("app/public/sliders/$slider->image"))) {
            File::delete(storage_path("app/public/sliders/$slider->image"));
        }

        if ($slider->mobile_image && File::exists(storage_path("app/public/sliders/$slider->mobile_image"))) {
            File::delete(storage_path("app/public/sliders/$slider->mobile_image"));
        }

        $slider->delete();
        toastr()->success('Slider Deleted Successfully!');
        return redirect()->route('sliders');
    }

    public function updateOrder(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            Slider::where('id', $item['id'])->update(['display_order' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }
}
