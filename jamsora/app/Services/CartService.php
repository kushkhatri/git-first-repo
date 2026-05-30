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
            ->with(['items.product.images', 'items.product.certification'])
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

    public function add(Product $product, int $quantity = 1, array $options = []): Cart
    {
        $cart = $this->getCart();
        $quantity = max(1, $quantity);
        $options = $this->normalizeOptions($product, $options);

        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->update([
                'quantity' => $item->quantity + $quantity,
                'unit_price' => $product->effective_price,
                'options' => array_merge($item->options ?? [], $options),
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->effective_price,
                'options' => $options,
            ]);
        }

        return $cart->fresh(['items.product.images', 'items.product.certification']);
    }

    public function updateQuantity(CartItem $item, int $quantity): void
    {
        if ($quantity < 1) {
            $item->delete();

            return;
        }

        $item->update(['quantity' => $quantity]);
    }

    public function updateOptions(CartItem $item, array $options): void
    {
        $product = $item->product;
        $item->update([
            'options' => $this->normalizeOptions($product, array_merge($item->options ?? [], $options)),
        ]);
    }

    public function remove(CartItem $item): void
    {
        $item->delete();
    }

    public function subtotal(Cart $cart): float
    {
        return $cart->items->sum(fn (CartItem $item) => $item->lineTotal());
    }

    public function igiTotal(Cart $cart): float
    {
        return $cart->items->sum(fn (CartItem $item) => $item->igiFee() * $item->quantity);
    }

    public function merchandiseSubtotal(Cart $cart): float
    {
        return $cart->items->sum(fn (CartItem $item) => (float) $item->unit_price * $item->quantity);
    }

    public function maxDeliveryDays(Cart $cart, bool $includeIgiOnItems = true): array
    {
        $min = 10;
        $max = 14;

        foreach ($cart->items as $item) {
            $product = $item->product;
            if (! $product) {
                continue;
            }
            $pMin = (int) $product->delivery_days_min;
            $pMax = (int) $product->delivery_days_max;
            $min = max($min, $pMin);
            $max = max($max, $pMax);

            if ($includeIgiOnItems && $item->hasIgi()) {
                $extra = (int) config('jamsora.igi.extra_delivery_days', 7);
                $min += $extra;
                $max += $extra;
            }
        }

        return ['min' => $min, 'max' => $max];
    }

    public function count(Cart $cart): int
    {
        return (int) $cart->items->sum('quantity');
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }

    private function normalizeOptions(Product $product, array $options): array
    {
        $igi = ! empty($options['igi_certification']) && $product->igi_available;

        return [
            'igi_certification' => $igi,
            'igi_fee' => $igi ? (float) config('jamsora.igi.fee', 100) : 0,
        ];
    }
}
