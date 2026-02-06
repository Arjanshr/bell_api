<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;

class AreaController extends Controller
{
    public function index(Request $request) {
        $provinceId = $request->get('province_id', []);
        $cityId = $request->get('city_id', []);
        $search = $request->get('search', null);
        $areas = Area::query();
        if (!empty($cityId)) {
            $areas->whereIn('city_id', (array)$cityId);
        }
        if (!empty($provinceId)) {
            $areas->whereHas('city', function($q) use ($provinceId) {
                $q->whereIn('province_id', (array)$provinceId);
            });
        }
        if ($search) {
            $areas->where('name', 'like', "%$search%");
        }
        $areas = $areas->get();
        $provinces = \App\Models\Province::all();
        $cities = \App\Models\City::all();
        return view('admin.areas.index', compact('areas', 'provinces', 'cities', 'provinceId', 'cityId', 'search'));
    }
    public function create() {
        return view('admin.areas.create');
    }
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'shipping_fee' => 'required|numeric|min:0',
        ]);
        Area::create($request->only('name', 'shipping_fee'));
        return redirect()->route('admin.locations.areas.index')->with('success', 'Area created!');
    }
    public function edit(Area $area) {
        return view('admin.areas.edit', compact('area'));
    }
    public function update(Request $request, Area $area) {
        $request->validate([
            'name' => 'required|string|max:255',
            'shipping_fee' => 'required|numeric|min:0',
        ]);
        $area->update($request->only('name', 'shipping_fee'));
        return redirect()->route('admin.locations.areas.index')->with('success', 'Area updated!');
    }
    public function destroy(Area $area) {
        $area->delete();
        return redirect()->route('admin.locations.areas.index')->with('success', 'Area deleted!');
    }
    /**
     * Mass update shipping price for selected areas.
     */
    public function massUpdatePrice(Request $request)
    {
        $request->validate([
            'area_ids' => 'required|array',
            'area_ids.*' => 'exists:areas,id',
            'new_price' => 'required|numeric|min:0',
            'operation' => 'required|in:set,add,subtract',
        ]);

        $areas = Area::whereIn('id', $request->area_ids)->get();
        foreach ($areas as $area) {
            $current = $area->shipping_price;
            $amount = $request->new_price;
            switch ($request->operation) {
                case 'add':
                    $area->shipping_price = $current + $amount;
                    break;
                case 'subtract':
                    $area->shipping_price = max(0, $current - $amount);
                    break;
                default:
                    $area->shipping_price = $amount;
            }
            $area->save();
        }

        if ($request->ajax()) {
            return response()->json(['message' => 'Shipping prices updated successfully!']);
        }
        return redirect()->back()->with('success', 'Shipping prices updated successfully!');
    }

    /**
     * Mass delete selected areas.
     */
    public function massDelete(Request $request)
    {
        $this->authorize('delete-areas');
        $request->validate([
            'area_ids' => 'required|array',
            'area_ids.*' => 'exists:areas,id',
        ]);

        Area::whereIn('id', $request->area_ids)->delete();

        if ($request->ajax()) {
            return response()->json(['message' => 'Selected areas deleted successfully!']);
        }
        return redirect()->back()->with('success', 'Selected areas deleted successfully!');
    }
}
