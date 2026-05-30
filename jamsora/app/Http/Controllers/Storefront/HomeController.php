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

        $categories = Category::query()
            ->where('status', 'active')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->take(12)
            ->get();

        return view('storefront.home', [
            'slider' => $slider,
            'featured' => $featured,
            'categories' => $categories,
            'testimonials' => Testimonial::query()->where('status', 'active')->orderBy('sort_order')->take(3)->get(),
            'siteName' => $settings->get('site_name', 'Jamsora', 'store'),
            'tagline' => $settings->get('tagline', 'Fine gemstones & jewelry', 'store'),
        ]);
    }
}
