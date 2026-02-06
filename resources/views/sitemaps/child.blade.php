@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($items as $item)
        <url>
            @if($type === 'category_brand')
                <loc>{{ config('app.frontend_url') }}/category/{{ $item->category_slug }}/brand/{{ $item->brand_slug }}</loc>
                <lastmod>{{ \Carbon\Carbon::parse($item->updated_at)->toAtomString() }}</lastmod>
            @elseif($type === 'product_images' || $type === 'blog_images')
                <loc>{{ $item->image_url }}</loc>
                <lastmod>{{ \Carbon\Carbon::parse($item->updated_at)->toAtomString() }}</lastmod>
            @else
                <loc>{{ config('app.frontend_url') }}/{{ $type }}/{{ $item->slug }}</loc>
                <lastmod>{{ $item->updated_at->toAtomString() }}</lastmod>
            @endif
            <changefreq>{{ $changefreq }}</changefreq>
            <priority>{{ $priority }}</priority>
        </url>
    @endforeach
</urlset>
