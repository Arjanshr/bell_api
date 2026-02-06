@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($sitemaps as $map)
    <sitemap>
        <loc>{{ config('app.frontend_url') }}/sitemaps/{{ $map }}.xml</loc>
    </sitemap>
    @endforeach
</sitemapindex>
