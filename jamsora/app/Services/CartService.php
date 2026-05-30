<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function getCart(): Cart
    {
        $sessionId = Session::getId();
        $userId = Auth::id();

        $cart = Cart::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(! $userId, fn ($q) => $q->where('session_id', $sessionId))
            ->with(['items.product.images'])
            ->first();

        if (! $cart) {
            $cart = Cart::create([
                'session_id' => $userId ? null : $sessionId,
                'user_id' => $userId,
                'ip_address' => request()->ip(),
            ]);
        }

        return $cart;
    }

    public function add(Product $product, int $quantity = 1): Cart
    {
        $cart = $this->getCart();
        $quantity = max(1, $quantity);

        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->update([
                'quantity' => $item->quantity + $quantity,
                'unit_price' => $product->effective_price,
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->effective_price,
            ]);
        }

        return $cart->fresh(['items.product.images']);
    }

    public function updateQuantity(CartItem $item, int $quantity): void
    {
        if ($quantity < 1) {
            $item->delete();

            return;
        }

        $item->update(['quantity' => $quantity]);
    }

    public function remove(CartItem $item): void
    {
        $item->delete();
    }

    public function subtotal(Cart $cart): float
    {
        return $cart->items->sum(fn (CartItem $item) => $item->lineTotal());
    }

    public function count(Cart $cart): int
    {
        return (int) $cart->items->sum('quantity');
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }
}
