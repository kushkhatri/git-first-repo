@extends('layouts.storefront')
@section('title', 'Cart')
@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <h1 class="font-serif text-4xl mb-8">Shopping cart</h1>
    @if($cart->items->isEmpty())
        <p class="text-stone-500">Your cart is empty. <a href="{{ route('shop.index') }}" class="text-amber-800">Continue shopping</a></p>
    @else
        <div class="space-y-4 mb-8">
            @foreach($cart->items as $item)
                <div class="flex gap-4 bg-white p-4 rounded-lg border">
                    @if($item->product->primaryImageUrl())
                        <img src="{{ $item->product->primaryImageUrl() }}" class="w-20 h-20 object-cover rounded" alt="">
                    @endif
                    <div class="flex-1">
                        <a href="{{ route('products.show', $item->product->slug) }}" class="font-medium">{{ $item->product->name }}</a>
                        <p class="text-sm text-stone-500">${{ number_format($item->unit_price, 2) }} each</p>
                    </div>
                    <form action="{{ route('cart.update', $item) }}" method="post" class="flex items-center gap-2">
                        @csrf @method('PATCH')
                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="w-16 rounded border-stone-300 text-sm">
                        <button class="text-sm text-amber-800">Update</button>
                    </form>
                    <form action="{{ route('cart.destroy', $item) }}" method="post">@csrf @method('DELETE')<button type="submit" class="text-red-600 text-sm">Remove</button></form>
                    <p class="font-semibold w-24 text-right">${{ number_format($item->lineTotal(), 2) }}</p>
                </div>
            @endforeach
        </div>
        <div class="bg-white p-6 rounded-lg border flex justify-between items-center">
            <span class="text-lg">Subtotal</span>
            <span class="text-2xl font-semibold text-amber-900">${{ number_format($subtotal, 2) }}</span>
        </div>
        <a href="{{ route('checkout.index') }}" class="mt-6 block text-center bg-amber-800 text-white py-4 rounded-full font-medium hover:bg-amber-700">Proceed to checkout</a>
    @endif
</div>
@endsection
