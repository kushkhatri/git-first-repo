<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderService $orderService,
    ) {}

    public function index()
    {
        $cart = $this->cartService->getCart();
        $cart->load('items.product.images');

        if ($cart->items->isEmpty()) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }

        return view('storefront.checkout.index', [
            'cart' => $cart,
            'subtotal' => $this->cartService->subtotal($cart),
            'igiTotal' => $this->cartService->igiTotal($cart),
            'delivery' => $this->cartService->maxDeliveryDays($cart),
        ]);
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'zip' => ['required', 'string', 'max:20'],
            'payment_method' => ['required', 'in:cod,stripe,razorpay'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['shipping_cost'] = 0;
        $validated['tax'] = 0;

        $order = $this->orderService->createFromCart($validated);

        return redirect()->route('checkout.success', $order)->with('success', 'Order placed successfully.');
    }

    public function success(Order $order)
    {
        $order->load(['items', 'addresses']);

        return view('storefront.checkout.success', compact('order'));
    }
}
