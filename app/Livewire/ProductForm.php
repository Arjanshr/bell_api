<?php

namespace App\Livewire;

use App\Enums\CategoryType;
use App\Models\Brand;
use App\Models\Category;
use Livewire\Component;

class ProductForm extends Component
{
    public  $brands;
    public $categories;
    public $message;
    public $name;
    public $price;
    public $warranty;
    public $product;
    public $description;
    public $alt_text;
    public $keywords;
    public $slug; // Add slug property
    public $short_description; // Add short_description property
    public $in_stock;

    public function mount()
    {
        $this->categories = Category::with('children')
            ->doesntHave('children')
            ->get();
        $this->brands = Brand::get();
        if($this->product){
            $this->name = $this->product->name;
            $this->price = $this->product->price;
            $this->warranty = $this->product->warranty;
            $this->description = $this->product->description;
            $this->short_description = $this->product->short_description; // Set short_description
            $this->alt_text = $this->product->alt_text;
            $this->keywords = explode(',', $this->product->keywords); // Split keywords into an array
            $this->slug = $this->product->slug; // Set slug when editing
            $this->in_stock = $this->product->in_stock;
        }
    }

    public function render()
    {
        return view('admin.livewire.product-form');
    }
}
