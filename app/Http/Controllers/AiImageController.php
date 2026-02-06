<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiImageController extends Controller
{
    private const ALLOWED_MODES = [
        // Core AI Features
        'remove_background',
        'replace_background',
        'auto_crop',
        'upscale',
        'enhance',
        'add_shadow',

        // Generative AI (May require paid plan)
        'gen_remove',
        'gen_replace',
        'gen_background_replace',
        'gen_recolor',
        'gen_restore',

        // Color & Brightness
        'auto_brightness',
        'auto_color',
        'auto_contrast',
        'auto_enhance',
        'improve',
        'grayscale',
        'sepia',
        'colorize',
        'vibrance',
        'saturation',
        'hue',
        'brightness',
        'contrast',
        'gamma',

        // Artistic Effects
        'cartoonify',
        'oil_paint',
        'blur',
        'blur_faces',
        'pixelate',
        'pixelate_faces',
        'vignette',
        'tint',
        'outline',

        // Advanced
        'distort',
        'trim',
        'sharpen',
        'unsharp_mask',
        'fill_light',
        'replace_color',
        'theme',
        'redeye',
        'blackwhite',
        'negate',
    ];

    private const CLOUDINARY_FOLDER = 'mobilemandu_ai_edits';

    /**
     * Edit a product image using AI transformations
     * POST /admin/products/{product}/media/{uuid}/ai-edit
     */
    public function edit(Request $request, Product $product, $uuid)
    {
        $validated = $request->validate([
            'mode' => 'required|string',
            'bg_color' => 'nullable|string',
        ]);

        if (!in_array($validated['mode'], self::ALLOWED_MODES, true)) {
            return response()->json(['error' => 'Invalid mode provided'], 422);
        }

        // Check if mode requires paid plan
        $paidInfo = $this->getPaidPlanInfo($validated['mode']);
        if ($paidInfo) {
            Log::info("Mode '{$validated['mode']}' used - {$paidInfo}");
        }

        $media = $product->getMedia()->firstWhere('uuid', $uuid);
        if (!$media) {
            return response()->json(['error' => 'Media not found'], 404);
        }

        $localPath = $this->getMediaPath($media);
        if (!$localPath) {
            return response()->json(['error' => 'Failed to retrieve media contents'], 502);
        }

        $tempPath = str_contains($localPath, sys_get_temp_dir()) ? $localPath : null;

        try {
            $transformation = $this->buildTransformationForMode(
                $validated['mode'],
                $validated['bg_color'] ?? 'white'
            );

            $uploadResult = $this->uploadToCloudinary($localPath);
            $publicId = $uploadResult['public_id'] ?? null;
            $format = $uploadResult['format'] ?? 'jpg';
            $cloudName = config('services.cloudinary.cloud_name') ?: env('CLOUDINARY_CLOUD_NAME');

            if (!$publicId || !$cloudName) {
                Log::error('Cloudinary upload failed', $uploadResult);
                $this->cleanupTempFile($tempPath);
                return response()->json(['error' => 'Image processing failed'], 500);
            }

            $transformedUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/{$transformation}/{$publicId}.{$format}";

            $newMedia = $this->saveTransformedImage($product, $media, $transformedUrl, $validated['mode'], $format);

            $this->cleanupTempFile($tempPath);

            return response()->json([
                'success' => true,
                'new_media_uuid' => $newMedia->uuid,
                'new_media_url' => $newMedia->getUrl(),
            ]);
        } catch (\Exception $e) {
            Log::error('AI image edit failed', ['exception' => $e->getMessage()]);
            $this->cleanupTempFile($tempPath);
            return response()->json(['error' => 'Image processing failed'], 500);
        }
    }

    private function getMediaPath($media): ?string
    {
        $localPath = $media->getPath();

        if (file_exists($localPath)) {
            return $localPath;
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'ai_');
        if ($tempPath === false || !@copy($media->getUrl(), $tempPath)) {
            if (is_file($tempPath)) {
                @unlink($tempPath);
            }
            return null;
        }

        return $tempPath;
    }

    private function uploadToCloudinary(string $localPath): array
    {
        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name') ?: env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => config('services.cloudinary.api_key') ?: env('CLOUDINARY_API_KEY'),
                'api_secret' => config('services.cloudinary.api_secret') ?: env('CLOUDINARY_API_SECRET'),
            ],
        ]);

        return (array)$cloudinary->uploadApi()->upload($localPath, [
            'folder' => self::CLOUDINARY_FOLDER,
            'resource_type' => 'image',
            'use_filename' => true,
            'unique_filename' => true,
        ]);
    }

    private function saveTransformedImage(Product $product, $originalMedia, string $transformedUrl, string $mode, string $format)
    {
        $contents = @file_get_contents($transformedUrl);
        if ($contents === false) {
            throw new \Exception('Failed to fetch transformed image');
        }

        $tempName = "ai_edited_{$originalMedia->id}_" . time() . ".{$format}";
        $tmpPath = storage_path("app/public/temp/{$tempName}");

        if (!is_dir(dirname($tmpPath))) {
            mkdir(dirname($tmpPath), 0755, true);
        }

        file_put_contents($tmpPath, $contents);

        $newMedia = $product->addMedia($tmpPath)
            ->usingFileName($tempName)
            ->withCustomProperties([
                'ai_edited_from' => $originalMedia->uuid,
                'ai_mode' => $mode,
                'alt_text' => $originalMedia->getCustomProperty('alt_text', ''),
            ])
            ->toMediaCollection();

        @unlink($tmpPath);

        return $newMedia;
    }

    private function cleanupTempFile(?string $tempPath): void
    {
        if ($tempPath && is_file($tempPath)) {
            @unlink($tempPath);
        }
    }

    protected function buildTransformationForMode(string $mode, string $bgColor = 'white'): string
    {
        return match ($mode) {
            // Core AI Features
            'remove_background' => 'e_background_removal',
            'replace_background' => 'e_background_removal/b_' . rawurlencode($bgColor),
            'auto_crop' => 'c_thumb,g_auto,w_1000,h_1000',
            'upscale' => 'e_upscale',
            'enhance' => 'e_enhance',
            'add_shadow' => 'e_shadow:50',

            // Generative AI (May require paid plan or special transformation counts)
            'gen_remove' => 'e_gen_remove',
            'gen_replace' => 'e_gen_replace:from_object;to_replacement',
            'gen_background_replace' => 'e_gen_background_replace',
            'gen_recolor' => 'e_gen_recolor:to_color',
            'gen_restore' => 'e_gen_restore',

            // Color & Brightness Adjustments
            'auto_brightness' => 'e_auto_brightness',
            'auto_color' => 'e_auto_color',
            'auto_contrast' => 'e_auto_contrast',
            'auto_enhance' => 'e_auto_enhance',
            'improve' => 'e_improve',
            'grayscale' => 'e_grayscale',
            'sepia' => 'e_sepia:40',
            'colorize' => 'e_colorize',
            'vibrance' => 'e_vibrance:40',
            'saturation' => 'e_saturation:30',
            'hue' => 'e_hue:20',
            'brightness' => 'e_brightness:20',
            'contrast' => 'e_contrast:20',
            'gamma' => 'e_gamma:1.3',

            // Artistic Effects
            'cartoonify' => 'e_cartoonify:80',
            'oil_paint' => 'e_oil_paint:50',
            'blur' => 'e_blur:300',
            'blur_faces' => 'e_blur_faces:800',
            'pixelate' => 'e_pixelate:10',
            'pixelate_faces' => 'e_pixelate_faces:10',
            'vignette' => 'e_vignette:30',
            'tint' => 'e_tint:50:FF0000',
            'outline' => 'e_outline:10:inner',

            // Advanced Effects
            'distort' => 'e_distort:arc:45',
            'trim' => 'e_trim',
            'sharpen' => 'e_sharpen:400',
            'unsharp_mask' => 'e_unsharp_mask:500',
            'fill_light' => 'e_fill_light:50',
            'replace_color' => 'e_replace_color:FF0000:FF00FF',
            'theme' => 'e_theme:color_FFD700',
            'redeye' => 'e_redeye',
            'blackwhite' => 'e_blackwhite:50',
            'negate' => 'e_negate',

            default => '',
        };
    }

    /**
     * Get paid plan requirements for a transformation mode
     */
    private function getPaidPlanInfo(string $mode): ?string
    {
        $paidFeatures = [
            'gen_remove' => 'Requires Cloudinary Generative AI plan',
            'gen_replace' => 'Requires Cloudinary Generative AI plan',
            'gen_background_replace' => 'Requires Cloudinary Generative AI plan',
            'gen_recolor' => 'Requires Cloudinary Generative AI plan',
            'gen_restore' => 'Requires Cloudinary Generative AI plan',
            'enhance' => 'May require higher tier plan',
            'upscale' => 'Special transformation count applies',
        ];

        return $paidFeatures[$mode] ?? null;
    }
}