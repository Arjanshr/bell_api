<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;

class CityController extends Controller
{
    public function index(Request $request) {
    $provinceId = $request->get('province_id');
    $search = $request->get('search', null);
        $cities = City::query();
        if ($provinceId) {
            $cities->where('province_id', $provinceId);
        }
        if ($search) {
            $cities->where('name', 'like', "%$search%");
        }
        $cities = $cities->get();
        $provinces = \App\Models\Province::all();
        return view('admin.cities.index', compact('cities', 'provinces', 'provinceId', 'search'));
    }
    public function create() {
    return view('admin.cities.create');
    }
    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:255']);
        City::create($request->only('name'));
        return redirect()->route('admin.locations.cities.index')->with('success', 'City created!');
    }
    public function edit(City $city) {
    return view('admin.cities.edit', compact('city'));
    }
    public function update(Request $request, City $city) {
        $request->validate(['name' => 'required|string|max:255']);
        $city->update($request->only('name'));
        return redirect()->route('admin.locations.cities.index')->with('success', 'City updated!');
    }
    public function destroy(City $city) {
        $city->delete();
        return redirect()->route('admin.locations.cities.index')->with('success', 'City deleted!');
    }
}
