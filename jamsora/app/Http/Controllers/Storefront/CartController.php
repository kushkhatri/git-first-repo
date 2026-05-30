<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    public function index()
    {
        $cart = $this->cartService->getCart();
        $cart->load('items.product.images');

        return view('storefront.cart.index', [
            'cart' => $cart,
            'subtotal' => $this->cartService->subtotal($cart),
            'igiTotal' => $this->cartService->igiTotal($cart),
            'delivery' => $this->cartService->maxDeliveryDays($cart),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:10'],
            'igi_certification' => ['nullable', 'boolean'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if (! $product->inStock()) {
            return back()->with('error', 'This gemstone is out of stock.');
        }

        $this->cartService->add($product, (int) ($validated['quantity'] ?? 1), [
            'igi_certification' => $request->boolean('igi_certification'),
        ]);

        return back()->with('success', 'Added to cart.');
    }

    public function update(Request $request, CartItem $item)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:10'],
            'igi_certification' => ['nullable', 'boolean'],
        ]);

        if ($request->has('igi_certification')) {
            $this->cartService->updateOptions($item, [
                'igi_certification' => $request->boolean('igi_certification'),
            ]);
        }

        $this->cartService->updateQuantity($item, (int) $validated['quantity']);

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function destroy(CartItem $item)
    {
        $this->cartService->remove($item);

        return redirect()->route('cart.index')->with('success', 'Item removed.');
    }
}
