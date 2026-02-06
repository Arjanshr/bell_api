<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{

    
    public function removeBackground(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // Max 5MB
        ]);
    
        $file = $request->file('image');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $originalPath = storage_path('app/public/originals/' . $filename);
        $removedPath = storage_path('app/public/removed/removed-' . $filename);
    
        // Ensure directories exist
        if (!file_exists(dirname($removedPath))) {
            mkdir(dirname($removedPath), 0775, true);
        }
        if (!file_exists(dirname($originalPath))) {
            mkdir(dirname($originalPath), 0775, true);
        }
    
        // Save original image
        $file->move(dirname($originalPath), basename($originalPath));
    
        // Make API call to remove.bg
        $apiKey = env('REMOVE_BG_API_KEY');
    
        $response = Http::attach(
            'image_file', file_get_contents($originalPath), $filename
        )->withHeaders([
            'X-Api-Key' => $apiKey
        ])->post('https://api.remove.bg/v1.0/removebg');
    
        if ($response->successful()) {
            file_put_contents($removedPath, $response->body());
            return response()->download($removedPath);
        }
    
        return response()->json([
            'error' => 'Background removal failed',
            'details' => $response->json(),
        ], 500);
    }
    
}
