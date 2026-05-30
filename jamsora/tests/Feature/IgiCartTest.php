<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IgiCartTest extends TestCase
{
    use RefreshDatabase;

    public function test_igi_adds_fee_and_delivery_days(): void
    {
        $category = Category::create(['name' => 'Sapphire', 'slug' => 'sapphire', 'status' => 'active']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Sapphire',
            'slug' => 'test-sapphire',
            'sku' => 'TEST-1',
            'price' => 500,
            'stock_qty' => 5,
            'manage_stock' => true,
            'status' => 'published',
            'delivery_days_min' => 10,
            'delivery_days_max' => 14,
            'igi_available' => true,
        ]);

        $cartService = app(CartService::class);
        $cartService->add($product, 1, ['igi_certification' => true]);
        $cart = $cartService->getCart();

        $this->assertEquals(100, $cartService->igiTotal($cart));
        $this->assertEquals(600, $cartService->subtotal($cart));

        $delivery = $cartService->maxDeliveryDays($cart);
        $this->assertEquals(17, $delivery['min']);
        $this->assertEquals(21, $delivery['max']);
    }

    public function test_add_to_cart_with_igi_via_http(): void
    {
        $product = Product::create([
            'name' => 'HTTP Gem',
            'slug' => 'http-gem',
            'sku' => 'HTTP-1',
            'price' => 200,
            'stock_qty' => 3,
            'manage_stock' => true,
            'status' => 'published',
            'igi_available' => true,
        ]);

        $response = $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
            'igi_certification' => true,
        ]);

        $response->assertRedirect();
        $cart = app(CartService::class)->getCart();
        $this->assertTrue($cart->items->first()->hasIgi());
    }
}
