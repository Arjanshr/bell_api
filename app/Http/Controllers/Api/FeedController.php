<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use App\Models\Product;

class FeedController extends BaseController
{
    public function generateFeed()
    {
        $products = Product::where('status', 1)->get();

        $xml = new \SimpleXMLElement('<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0"></rss>');
        $channel = $xml->addChild('channel');
        $channel->addChild('title', 'Mobile Mandu Product Feed');
        $channel->addChild('link', url('/'));
        $channel->addChild('description', 'Google Merchant Feed for MobileMandu');

        foreach ($products as $product) {
            $item = $channel->addChild('item');
            $item->addChild('g:id', $product->id, 'http://base.google.com/ns/1.0');
            $item->addChild('g:title', htmlspecialchars($product->name), 'http://base.google.com/ns/1.0');
            $item->addChild('g:description', htmlspecialchars(strip_tags($product->description)), 'http://base.google.com/ns/1.0');
            $item->addChild('g:link', "https://mobilemandu.com/products/$product->slug", 'http://base.google.com/ns/1.0');
            $item->addChild('g:image_link', $product->getFirstMedia() ? $product->getFirstMedia()->getUrl() : null, 'http://base.google.com/ns/1.0');
            $item->addChild('g:availability', 'in stock', 'http://base.google.com/ns/1.0');
            $item->addChild('g:price', number_format($product->price, 2) . ' NPR', 'http://base.google.com/ns/1.0');
            $item->addChild('g:brand', htmlspecialchars($product->brand->name ?? 'Unknown'), 'http://base.google.com/ns/1.0');
            $item->addChild('g:condition', 'new', 'http://base.google.com/ns/1.0');
        }

        return response($xml->asXML(), 200)->header('Content-Type', 'application/xml');
    }
}
