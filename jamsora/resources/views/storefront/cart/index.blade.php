@extends('layouts.storefront')
@section('title', 'Cart')
@section('content')
<div class="max-w-4xl mx-auto px-4 py-16">
    <h1 class="section-title mb-10">Shopping cart</h1>
    @if($cart->items->isEmpty())
        <p class="text-jamsora-muted">Your cart is empty. <a href="{{ route('shop.index') }}" class="text-jamsora-ink underline underline-offset-4">Continue shopping</a></p>
    @else
        <div class="space-y-4 mb-10">
            @foreach($cart->items as $item)
                <div class="flex gap-5 bg-white p-5 border border-jamsora-border">
                    @if($item->product->primaryImageUrl())
                        <img src="{{ $item->product->primaryImageUrl() }}" class="w-24 h-24 object-cover" alt="">
                    @endif
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('products.show', $item->product->slug) }}" class="font-display text-lg text-jamsora-ink hover:text-jamsora-champagne-dark">{{ $item->product->name }}</a>
                        <p class="text-sm text-jamsora-muted mt-1">${{ number_format($item->unit_price, 2) }} each</p>
                    </div>
                    <form action="{{ route('cart.update', $item) }}" method="post" class="flex items-center gap-2">
                        @csrf @method('PATCH')
                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="input-field w-16">
                        <button class="text-xs uppercase tracking-widest text-jamsora-ink">Update</button>
                    </form>
                    <form action="{{ route('cart.destroy', $item) }}" method="post">@csrf @method('DELETE')<button type="submit" class="text-xs uppercase tracking-widest text-red-800">Remove</button></form>
                    <p class="font-medium text-jamsora-ink w-24 text-right">${{ number_format($item->lineTotal(), 2) }}</p>
                </div>
            @endforeach
        </div>
        <div class="bg-white p-6 border border-jamsora-border flex justify-between items-center">
            <span class="text-xs uppercase tracking-[0.2em] text-jamsora-muted">Subtotal</span>
            <span class="text-2xl font-light text-jamsora-ink">${{ number_format($subtotal, 2) }}</span>
        </div>
        <a href="{{ route('checkout.index') }}" class="mt-8 block text-center btn-primary">Proceed to checkout</a>
    @endif
</div>
@endsection
