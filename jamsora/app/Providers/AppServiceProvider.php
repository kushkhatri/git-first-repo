<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\CartService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.storefront', function ($view) {
            $cart = app(CartService::class)->getCart();
            $view->with('cartCount', app(CartService::class)->count($cart));
            $view->with('headerCategories', Category::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->take(8)
                ->get());
        });
    }
}
