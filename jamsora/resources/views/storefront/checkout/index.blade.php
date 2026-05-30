@extends('layouts.storefront')
@section('title', 'Checkout')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-16">
    <h1 class="section-title mb-10">Checkout</h1>
    <form action="{{ route('checkout.process') }}" method="post" class="space-y-5 bg-white p-8 md:p-10 border border-jamsora-border">
        @csrf
        <div class="grid md:grid-cols-2 gap-5">
            <div><label class="block text-[10px] uppercase tracking-widest text-jamsora-muted mb-2">Full name</label><input name="name" required class="input-field" value="{{ old('name', auth()->user()?->name) }}"></div>
            <div><label class="block text-[10px] uppercase tracking-widest text-jamsora-muted mb-2">Email</label><input name="email" type="email" required class="input-field" value="{{ old('email', auth()->user()?->email) }}"></div>
        </div>
        <div><label class="block text-[10px] uppercase tracking-widest text-jamsora-muted mb-2">Phone</label><input name="phone" class="input-field" value="{{ old('phone') }}"></div>
        <div><label class="block text-[10px] uppercase tracking-widest text-jamsora-muted mb-2">Address</label><input name="address_line1" required class="input-field" value="{{ old('address_line1') }}"></div>
        <div class="grid md:grid-cols-3 gap-5">
            <div><label class="block text-[10px] uppercase tracking-widest text-jamsora-muted mb-2">City</label><input name="city" required class="input-field" value="{{ old('city') }}"></div>
            <div><label class="block text-[10px] uppercase tracking-widest text-jamsora-muted mb-2">State</label><input name="state" class="input-field" value="{{ old('state') }}"></div>
            <div><label class="block text-[10px] uppercase tracking-widest text-jamsora-muted mb-2">ZIP</label><input name="zip" required class="input-field" value="{{ old('zip') }}"></div>
        </div>
        <div><label class="block text-[10px] uppercase tracking-widest text-jamsora-muted mb-2">Country</label><input name="country" required class="input-field" value="{{ old('country', 'US') }}"></div>
        <div><label class="block text-[10px] uppercase tracking-widest text-jamsora-muted mb-2">Payment</label>
            <select name="payment_method" class="input-field">
                <option value="cod">Bank transfer / COD</option>
                <option value="stripe">Stripe</option>
                <option value="razorpay">Razorpay</option>
            </select>
        </div>
        <p class="text-sm pt-6 border-t border-jamsora-border flex justify-between">
            <span class="text-jamsora-muted uppercase tracking-widest text-xs">Order total</span>
            <strong class="text-xl font-light text-jamsora-ink">${{ number_format($subtotal, 2) }}</strong>
        </p>
        <button type="submit" class="w-full btn-primary">Place order</button>
    </form>
</div>
@endsection
