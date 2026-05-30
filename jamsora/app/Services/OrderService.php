<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(private readonly CartService $cartService) {}

    public function createFromCart(array $data): Order
    {
        $cart = $this->cartService->getCart();
        $cart->load('items.product');

        if ($cart->items->isEmpty()) {
            throw new \RuntimeException('Cart is empty.');
        }

        return DB::transaction(function () use ($cart, $data) {
            $merchandise = $this->cartService->merchandiseSubtotal($cart);
            $igiTotal = $this->cartService->igiTotal($cart);
            $subtotal = $this->cartService->subtotal($cart);
            $shipping = (float) ($data['shipping_cost'] ?? 0);
            $tax = (float) ($data['tax'] ?? 0);
            $discount = (float) ($data['discount'] ?? 0);
            $total = $subtotal + $shipping + $tax - $discount;
            $delivery = $this->cartService->maxDeliveryDays($cart);

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => 'JMS-'.strtoupper(Str::random(8)),
                'subtotal' => $merchandise,
                'discount' => $discount,
                'igi_total' => $igiTotal,
                'shipping_cost' => $shipping,
                'tax' => $tax,
                'total' => $total,
                'status' => OrderStatus::Pending->value,
                'payment_status' => 'pending',
                'payment_method' => $data['payment_method'] ?? 'cod',
                'notes' => $data['notes'] ?? null,
                'guest_email' => $data['email'] ?? null,
                'delivery_days_min' => $delivery['min'],
                'delivery_days_max' => $delivery['max'],
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'sku' => $item->product->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->lineTotal(),
                    'options' => $item->options,
                ]);

                if ($item->product->manage_stock) {
                    $item->product->decrement('stock_qty', $item->quantity);
                }
            }

            $order->addresses()->create([
                'type' => 'shipping',
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'address_line1' => $data['address_line1'],
                'address_line2' => $data['address_line2'] ?? null,
                'city' => $data['city'],
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? 'US',
                'zip' => $data['zip'],
            ]);

            $order->addresses()->create([
                'type' => 'billing',
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'address_line1' => $data['address_line1'],
                'address_line2' => $data['address_line2'] ?? null,
                'city' => $data['city'],
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? 'US',
                'zip' => $data['zip'],
            ]);

            $this->cartService->clear($cart);

            return $order->load(['items', 'addresses']);
        });
    }
}
