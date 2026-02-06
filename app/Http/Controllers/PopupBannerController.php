<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PopupBanner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PopupBannerController extends Controller
{
    public function index()
    {
        $popupBanner = PopupBanner::first(); // Fetch the popup banner settings from the database
        return view('admin.popup-banner.index', compact('popupBanner'));
    }

    public function update(Request $request, $id)
    {
        $popupBanner = PopupBanner::findOrFail($id);

        $validatedData = $request->validate([
            'image_url' => 'nullable|image|max:2048',
            'is_active' => 'required|in:0,1',
            'url' => 'nullable|url',
        ]);

        // Handle file upload
        if ($request->hasFile('image_url')) {
            // Delete old image if it exists
            if ($popupBanner->image_url && Storage::disk('public')->exists($popupBanner->image_url)) {
                Storage::disk('public')->delete($popupBanner->image_url);
                Log::info("Deleted old image: " . $popupBanner->image_url);
            } else {
                Log::warning("Old image not found: " . $popupBanner->image_url);
            }

            // Store new image in the "popup_banners" directory inside "storage/app/public"
            $imagePath = $request->file('image_url')->store('popup_banners', 'public');
            $popupBanner->image_url = $imagePath; // Save only the relative path
        }

        // Update is_active and url fields
        $popupBanner->is_active = (int) $request->input('is_active');
        $popupBanner->url = $request->input('url');
        $popupBanner->save();

        return redirect()->route('popup-banners.index')->with('success', 'Popup banner updated successfully');
    }
}
