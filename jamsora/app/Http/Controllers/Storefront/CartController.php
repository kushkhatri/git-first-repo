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
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if (! $product->inStock()) {
            return back()->with('error', 'This gemstone is out of stock.');
        }

        $this->cartService->add($product, (int) ($validated['quantity'] ?? 1));

        return back()->with('success', 'Added to cart.');
    }

    public function update(Request $request, CartItem $item)
    {
        $this->cartService->updateQuantity($item, (int) $request->validate(['quantity' => 'required|integer|min:0|max:10'])['quantity']);

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function destroy(CartItem $item)
    {
        $this->cartService->remove($item);

        return redirect()->route('cart.index')->with('success', 'Item removed.');
    }
}
