<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Enums\SettingType;
use Illuminate\Validation\Rules\Enum;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('key')->paginate(20);
        return view('admin.settings.index', compact('settings'));
    }

    public function show(Setting $setting)
    {
        return view('admin.settings.show', compact('setting'));
    }

    public function create()
    {
        $types = SettingType::cases(); // Enum-based options
        return view('admin.settings.create', compact('types'));
    }

    public function insert(Request $request)
    {
        $data = $request->validate([
            'key' => 'required|string|unique:settings,key',
            'value' => 'nullable',
            'type' => ['required', new Enum(SettingType::class)],
            'options' => 'nullable|string',
        ]);

        $data['type'] = SettingType::from($data['type']); // ✅ convert to enum

        if ($data['type']->value === 'select' && isset($data['options'])) {
            $data['options'] = array_map('trim', explode(',', $data['options']));
        } else {
            $data['options'] = null;
        }

        Setting::create($data);

        return redirect()->route('setting.index')->with('success', 'Setting created.');
    }

    public function edit(Setting $setting)
    {
        $types = SettingType::cases();
        return view('admin.settings.edit', compact('setting', 'types'));
    }

    public function update(Request $request, Setting $setting)
    {
        $data = $request->validate([
            'value' => 'nullable',
            'type' => ['required', new Enum(SettingType::class)],
            'options' => 'nullable|string',
        ]);

        $data['type'] = SettingType::from($data['type']); // ✅ convert to enum

        if ($data['type']->value === 'select' && isset($data['options'])) {
            $data['options'] = array_map('trim', explode(',', $data['options']));
        } else {
            $data['options'] = null;
        }

        $setting->update($data);

        return redirect()->route('setting.index')->with('success', 'Setting updated.');
    }

    public function delete(Setting $setting)
    {
        $setting->delete();
        Cache::forget("setting_{$setting->key}");

        return redirect()->route('setting.index')->with('success', 'Setting deleted.');
    }

    public function generalSettings()
    {
        // Load all settings (or only specific ones if needed)
        $settings = Setting::all();
        return view('admin.settings.general', compact('settings'));
    }


    public function updateGeneralSettings(Request $request)
    {
        foreach (Setting::all() as $setting) {
            $key = $setting->key;

            switch ($setting->type) {
                case SettingType::IMAGE:
                    if ($request->hasFile($key)) {
                        $file = $request->file($key);
                        $path = $file->store('settings', 'public');
                        $setting->value = 'storage/' . $path;
                    }
                    break;

                case SettingType::BOOLEAN:
                    $setting->value = $request->input($key, '0') === '1' ? '1' : '0';
                    break;

                default:
                    if ($request->has($key)) {
                        $setting->value = $request->input($key);
                    }
                    break;
            }

            $setting->save();
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
