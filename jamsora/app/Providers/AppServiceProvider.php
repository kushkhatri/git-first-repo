<?php

namespace App\Providers;

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
            $cart->loadCount('items');
            $view->with('cartCount', app(CartService::class)->count($cart));
        });
    }
}
