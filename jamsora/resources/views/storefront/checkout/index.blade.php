@extends('layouts.storefront')
@section('title', 'Checkout')

@section('content')
<section class="bg-jamsora-cream border-b border-jamsora-border py-14">
    <div class="max-w-site mx-auto px-4 text-center">
        <h1 class="font-display text-4xl text-jamsora-ink">Checkout</h1>
    </div>
</section>

<div class="max-w-2xl mx-auto px-4 lg:px-8 py-12 md:py-16">
    <form action="{{ route('checkout.process') }}" method="post" class="space-y-6 bg-white border border-jamsora-border p-8 md:p-10">
        @csrf
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs uppercase tracking-widest text-jamsora-muted mb-2">Full name</label>
                <input name="name" required class="w-full border border-jamsora-border px-4 py-3 text-sm focus:border-jamsora-gold focus:ring-0" value="{{ old('name', auth()->user()?->name) }}">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest text-jamsora-muted mb-2">Email</label>
                <input name="email" type="email" required class="w-full border border-jamsora-border px-4 py-3 text-sm focus:border-jamsora-gold focus:ring-0" value="{{ old('email', auth()->user()?->email) }}">
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
        <div class="flex justify-between items-center pt-6 border-t border-jamsora-border">
            <span class="font-display text-2xl">Total</span>
            <span class="font-display text-2xl text-jamsora-gold-dark">${{ number_format($subtotal, 2) }}</span>
        </div>
        <button type="submit" class="btn-dagas w-full">Place order</button>
    </form>
</div>
@endsection
