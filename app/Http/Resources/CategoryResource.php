<?php

namespace App\Http\Resources;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Sort subcategories by name
        $sortedChildren = $this->children->sortBy('name');
        $subcategories = [];
        foreach ($sortedChildren as $subcategory) {
            $subcategories[] = [
                'id' => $subcategory->id,
                'name' => $subcategory->name,
                'slug' => $subcategory->slug,
                // Sort brands by name for each subcategory
                'brand' => $this->brands($subcategory->products ?? []),
            ];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'summary' => $this->summary,
            'description' => $this->description,
            'slug' => $this->slug,
            'imageLink' => $this->image ? asset('storage/categories/' . $this->image) : asset('images/default.png'),
            'subcategories' => $subcategories,
            'brands' => $this->brands($this->products),
            "seo" => [
                "meta_title" => $this->meta_title??$this->name,
                "meta_description" => $this->meta_description??strip_tags($this->description),
            ],
        ];
    }

    private function brands($products)
    {
        $brand_ids = null;
        foreach ($products as $product) {
            if (isset($product->brand->id)) {
                $brand_ids[] = $product->brand->id;
            }
        }
        $brands = null;
        if ($brand_ids != null)
            // Sort brands by name
            $brands = Brand::select('id', 'name', 'slug')->whereIn('id', $brand_ids)->orderBy('name')->get();
        return $brands;
    }

    private function subcategories($subcategories)
    {
        if ($subcategories->count() == 0) {
            return null;
        } else {
            foreach ($subcategories as $subcategory) {
                // $subcategory = $this->subcategories($subcategory);
                return $subcategory;
            }
        }
        return $subcategories;
    }
}
