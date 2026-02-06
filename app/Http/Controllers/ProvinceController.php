<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Province;

class ProvinceController extends Controller
{
    public function index() {
    $provinces = Province::all();
    return view('admin.provinces.index', compact('provinces'));
    }
    public function create() {
    return view('admin.provinces.create');
    }
    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:255']);
        Province::create($request->only('name'));
        return redirect()->route('admin.locations.provinces.index')->with('success', 'Province created!');
    }
    public function edit(Province $province) {
    return view('provinces.edit', compact('province'));
    }
    public function update(Request $request, Province $province) {
        $request->validate(['name' => 'required|string|max:255']);
        $province->update($request->only('name'));
        return redirect()->route('admin.locations.provinces.index')->with('success', 'Province updated!');
    }
    public function destroy(Province $province) {
        $province->delete();
        return redirect()->route('admin.locations.provinces.index')->with('success', 'Province deleted!');
    }
}
