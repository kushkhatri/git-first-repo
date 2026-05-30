<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Slider;
use App\Models\Testimonial;
use App\Services\SettingService;

class HomeController extends Controller
{
    public function __invoke(SettingService $settings)
    {
        $slider = Slider::query()->where('status', 'active')->with('slides')->first();

        $categories = Category::query()
            ->where('status', 'active')
            ->withCount('products')
            ->orderBy('name')
            ->get();

        $featured = Product::query()
            ->where('status', 'published')
            ->where('is_featured', true)
            ->with(['images', 'category'])
            ->latest()
            ->take(8)
            ->get();

        if ($featured->isEmpty()) {
            $featured = Product::query()
                ->where('status', 'published')
                ->with(['images', 'category'])
                ->latest()
                ->take(8)
                ->get();
        }

        $newArrivals = Product::query()
            ->where('status', 'published')
            ->with(['images', 'category'])
            ->latest()
            ->take(8)
            ->get();

        $tabCategories = $categories->take(5);

        $productsByCategory = [];
        foreach ($tabCategories as $category) {
            $productsByCategory[$category->slug] = Product::query()
                ->where('status', 'published')
                ->where('category_id', $category->id)
                ->with(['images', 'category'])
                ->latest()
                ->take(8)
                ->get();
        }

        return view('storefront.home', [
            'slider' => $slider,
            'featured' => $featured,
            'newArrivals' => $newArrivals,
            'categories' => $categories,
            'tabCategories' => $tabCategories,
            'productsByCategory' => $productsByCategory,
            'testimonials' => Testimonial::query()->where('status', 'active')->orderBy('sort_order')->take(4)->get(),
            'siteName' => $settings->get('site_name', 'Jamsora', 'store'),
            'tagline' => $settings->get('tagline', 'Fine gemstones & jewelry', 'store'),
        ]);
    }
}
