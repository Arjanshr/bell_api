<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Blog;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    // Dynamic sitemap index
    public function index(): Response
    {
    $sitemaps = ['products', 'categories', 'brands', 'category_brand', 'blog', 'product_images', 'blog_images']; // add new types here

        return response()
            ->view('sitemaps.index', compact('sitemaps'))
            ->header('Content-Type', 'application/xml');
    }

    // Generic child sitemap
    public function child(string $type): Response
    {
        $data = [
            'products' => [
                'items' => Product::where('status', 'publish')->get(),
                'changefreq' => 'daily',
                'priority' => 0.6,
            ],
            'categories' => [
                'items' => Category::where('status', 'active')->get(),
                'changefreq' => 'weekly',
                'priority' => 0.8,
            ],
            'brands' => [
                'items' => Brand::get(),
                'changefreq' => 'weekly',
                'priority' => 0.7,
            ],
            'category_brand' => [
                'items' => Category::where('status', 'active')->with(['brands' => function($q) {
                    $q->select('brands.id', 'brands.slug');
                }])->get()->flatMap(function($category) {
                    return $category->brands->map(function($brand) use ($category) {
                        return (object) [
                            'category_slug' => $category->slug,
                            'brand_slug' => $brand->slug,
                            'updated_at' => $brand->pivot->updated_at ?? $brand->updated_at,
                        ];
                    });
                }),
                'changefreq' => 'weekly',
                'priority' => 0.6,
            ],
            'blog' => [
                'items' => Blog::where('status', 'publish')->get(),
                'changefreq' => 'weekly',
                'priority' => 0.7,
            ],
            'product_images' => [
                'items' => Product::where('status', 'publish')->get()->flatMap(function($product) {
                    return $product->getMedia('default')->map(function($media) {
                        return (object) [
                            'image_url' => $media->getUrl(),
                            'updated_at' => $media->updated_at,
                        ];
                    });
                }),
                'changefreq' => 'weekly',
                'priority' => 0.5,
            ],
            'blog_images' => [
                'items' => Blog::where('status', 'publish')->whereNotNull('image')->get()->map(function($blog) {
                    return (object) [
                        'image_url' => url('storage/blogs/' . $blog->image),
                        'updated_at' => $blog->updated_at,
                    ];
                }),
                'changefreq' => 'weekly',
                'priority' => 0.5,
            ],
        ];

        if (!isset($data[$type])) {
            abort(404);
        }

        return response()
            ->view('sitemaps.child', [
                'items' => $data[$type]['items'],
                'type' => $type,
                'changefreq' => $data[$type]['changefreq'],
                'priority' => $data[$type]['priority'],
            ])
            ->header('Content-Type', 'application/xml');
    }
}
