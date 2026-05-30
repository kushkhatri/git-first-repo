<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with(['images', 'category', 'tags'])
            ->firstOrFail();

        $related = Product::query()
            ->where('status', 'published')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('images')
            ->take(4)
            ->get();

        return view('storefront.products.show', compact('product', 'related'));
    }
}
