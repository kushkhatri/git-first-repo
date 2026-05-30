@extends('layouts.storefront')
@section('title', 'Cart')

@section('content')
<section class="bg-jamsora-cream border-b border-jamsora-border py-14">
    <div class="max-w-site mx-auto px-4 text-center">
        <h1 class="font-display text-4xl text-jamsora-ink">Shopping Cart</h1>
    </div>
</section>

<div class="max-w-site mx-auto px-4 lg:px-8 py-12 md:py-16">
    @if($cart->items->isEmpty())
        <div class="text-center py-20">
            <p class="text-jamsora-muted mb-6">Your cart is empty.</p>
            <a href="{{ route('shop.index') }}" class="btn-dagas">Continue shopping</a>
        </div>
    @else
        <div class="grid lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2 space-y-6">
                @foreach($cart->items as $item)
                    <div class="flex gap-5 border border-jamsora-border p-5 bg-white">
                        @if($item->product->primaryImageUrl())
                            <a href="{{ route('products.show', $item->product->slug) }}" class="w-28 h-36 flex-shrink-0 overflow-hidden bg-jamsora-cream">
                                <img src="{{ $item->product->primaryImageUrl() }}" alt="" class="w-full h-full object-cover">
                            </a>
                        @endif
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('products.show', $item->product->slug) }}" class="font-display text-xl hover:text-jamsora-gold transition">{{ $item->product->name }}</a>
                            <p class="text-sm text-jamsora-muted mt-1">${{ number_format($item->unit_price, 2) }} each</p>
                            @if($item->hasIgi())
                                <p class="text-xs text-jamsora-gold mt-1">IGI Certification (+${{ number_format($item->igiFee(), 2) }})</p>
                            @endif
                            <form action="{{ route('cart.update', $item) }}" method="post" class="mt-4 space-y-3">
                                @csrf @method('PATCH')
                                <div class="flex items-center gap-3">
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="w-16 border border-jamsora-border px-2 py-1 text-sm">
                                    <button class="text-xs uppercase tracking-widest text-jamsora-gold">Update qty</button>
                                </div>
                                @if($item->product->igi_available)
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="checkbox" name="igi_certification" value="1" @checked($item->hasIgi())>
                                        IGI Certification (+${{ number_format(config('jamsora.igi.fee', 100), 0) }})
                                    </label>
                                @endif
                            </form>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-lg">${{ number_format($item->lineTotal(), 2) }}</p>
                            <form action="{{ route('cart.destroy', $item) }}" method="post" class="mt-4">
                                @csrf @method('DELETE')
                                <button class="text-xs uppercase tracking-widest text-red-700">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="lg:col-span-1">
                <div class="border border-jamsora-border p-8 bg-jamsora-cream sticky top-28">
                    <h2 class="font-display text-2xl mb-6">Order summary</h2>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-jamsora-muted">Merchandise</span>
                        <span>${{ number_format($subtotal - ($igiTotal ?? 0), 2) }}</span>
                    </div>
                    @if(($igiTotal ?? 0) > 0)
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-jamsora-muted">IGI Certification</span>
                            <span>${{ number_format($igiTotal, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-jamsora-muted">Est. delivery</span>
                        <span>{{ $delivery['min'] ?? 10 }}-{{ $delivery['max'] ?? 14 }} days</span>
                    </div>
                    <div class="flex justify-between text-sm mb-6 pb-6 border-b border-jamsora-border">
                        <span class="text-jamsora-muted">Shipping</span>
                        <span class="text-jamsora-muted">At checkout</span>
                    </div>
                    <div class="flex justify-between font-display text-2xl mb-8">
                        <span>Total</span>
                        <span class="text-jamsora-gold-dark">${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="btn-dagas w-full text-center">Proceed to checkout</a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
