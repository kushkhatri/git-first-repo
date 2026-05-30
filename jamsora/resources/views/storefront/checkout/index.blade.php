@extends('layouts.storefront')
@section('title', 'Checkout')

@section('content')
<section class="bg-jamsora-cream border-b border-jamsora-border py-14">
    <div class="max-w-site mx-auto px-4 text-center">
        <h1 class="font-display text-4xl text-jamsora-ink">Checkout</h1>
    </div>
</section>

<div class="max-w-site mx-auto px-4 lg:px-8 py-12 md:py-16 grid lg:grid-cols-3 gap-12">
    <form action="{{ route('checkout.process') }}" method="post" class="lg:col-span-2 space-y-6 bg-white border border-jamsora-border p-8">
        @csrf
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs uppercase tracking-widest text-jamsora-muted mb-2">Full name</label>
                <input name="name" required class="w-full border border-jamsora-border px-4 py-3 text-sm" value="{{ old('name', auth()->user()?->name) }}">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest text-jamsora-muted mb-2">Email</label>
                <input name="email" type="email" required class="w-full border border-jamsora-border px-4 py-3 text-sm" value="{{ old('email', auth()->user()?->email) }}">
            </div>
        </div>
        <div>
            <label class="block text-xs uppercase tracking-widest text-jamsora-muted mb-2">Phone</label>
            <input name="phone" class="w-full border border-jamsora-border px-4 py-3 text-sm" value="{{ old('phone') }}">
        </div>
        <div>
            <label class="block text-xs uppercase tracking-widest text-jamsora-muted mb-2">Address</label>
            <input name="address_line1" required class="w-full border border-jamsora-border px-4 py-3 text-sm" value="{{ old('address_line1') }}">
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs uppercase tracking-widest text-jamsora-muted mb-2">City</label>
                <input name="city" required class="w-full border border-jamsora-border px-4 py-3 text-sm" value="{{ old('city') }}">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest text-jamsora-muted mb-2">State</label>
                <input name="state" class="w-full border border-jamsora-border px-4 py-3 text-sm" value="{{ old('state') }}">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest text-jamsora-muted mb-2">ZIP</label>
                <input name="zip" required class="w-full border border-jamsora-border px-4 py-3 text-sm" value="{{ old('zip') }}">
            </div>
        </div>
        <div>
            <label class="block text-xs uppercase tracking-widest text-jamsora-muted mb-2">Country</label>
            <input name="country" required class="w-full border border-jamsora-border px-4 py-3 text-sm" value="{{ old('country', 'US') }}">
        </div>
        <div>
            <label class="block text-xs uppercase tracking-widest text-jamsora-muted mb-2">Payment</label>
            <select name="payment_method" class="w-full border border-jamsora-border px-4 py-3 text-sm bg-white">
                <option value="cod">Bank transfer / COD</option>
                <option value="stripe">Stripe</option>
                <option value="razorpay">Razorpay</option>
            </select>
        </div>
        <button type="submit" class="btn-dagas w-full">Place order</button>
    </form>

    <div class="border border-jamsora-border p-8 bg-jamsora-cream h-fit">
        <h2 class="font-display text-xl mb-6">Order summary</h2>
        <ul class="text-sm space-y-3 mb-6">
            @foreach($cart->items as $item)
                <li>
                    <p class="font-medium">{{ $item->product->name }} × {{ $item->quantity }}</p>
                    @if($item->hasIgi())
                        <p class="text-xs text-jamsora-gold">IGI: Yes (+${{ number_format($item->igiFee() * $item->quantity, 2) }})</p>
                    @endif
                </li>
            @endforeach
        </ul>
        @if(($igiTotal ?? 0) > 0)
            <p class="flex justify-between text-sm mb-2"><span>IGI certification</span><span>${{ number_format($igiTotal, 2) }}</span></p>
        @endif
        <p class="flex justify-between text-sm mb-2 text-jamsora-muted"><span>Est. delivery</span><span>{{ $delivery['min'] }}-{{ $delivery['max'] }} days</span></p>
        <p class="flex justify-between font-display text-2xl pt-4 border-t border-jamsora-border">
            <span>Total</span>
            <span class="text-jamsora-gold-dark">${{ number_format($subtotal, 2) }}</span>
        </p>
    </div>
</div>
@endsection
