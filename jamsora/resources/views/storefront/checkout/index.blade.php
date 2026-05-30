@extends('layouts.storefront')
@section('title', 'Checkout')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <h1 class="font-serif text-4xl mb-8">Checkout</h1>
    <form action="{{ route('checkout.process') }}" method="post" class="space-y-4 bg-white p-8 rounded-lg border">
        @csrf
        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="block text-sm mb-1">Full name</label><input name="name" required class="w-full rounded-lg border-stone-300" value="{{ old('name', auth()->user()?->name) }}"></div>
            <div><label class="block text-sm mb-1">Email</label><input name="email" type="email" required class="w-full rounded-lg border-stone-300" value="{{ old('email', auth()->user()?->email) }}"></div>
        </div>
        <div><label class="block text-sm mb-1">Phone</label><input name="phone" class="w-full rounded-lg border-stone-300" value="{{ old('phone') }}"></div>
        <div><label class="block text-sm mb-1">Address</label><input name="address_line1" required class="w-full rounded-lg border-stone-300" value="{{ old('address_line1') }}"></div>
        <div class="grid md:grid-cols-3 gap-4">
            <div><label class="block text-sm mb-1">City</label><input name="city" required class="w-full rounded-lg border-stone-300" value="{{ old('city') }}"></div>
            <div><label class="block text-sm mb-1">State</label><input name="state" class="w-full rounded-lg border-stone-300" value="{{ old('state') }}"></div>
            <div><label class="block text-sm mb-1">ZIP</label><input name="zip" required class="w-full rounded-lg border-stone-300" value="{{ old('zip') }}"></div>
        </div>
        <div><label class="block text-sm mb-1">Country</label><input name="country" required class="w-full rounded-lg border-stone-300" value="{{ old('country', 'US') }}"></div>
        <div><label class="block text-sm mb-1">Payment</label>
            <select name="payment_method" class="w-full rounded-lg border-stone-300">
                <option value="cod">Cash on Delivery / Bank Transfer</option>
                <option value="stripe">Stripe (configure keys)</option>
                <option value="razorpay">Razorpay (configure keys)</option>
            </select>
        </div>
        <p class="text-lg pt-4 border-t">Order total: <strong class="text-amber-900">${{ number_format($subtotal, 2) }}</strong></p>
        <button type="submit" class="w-full bg-amber-800 text-white py-4 rounded-full font-medium hover:bg-amber-700">Place order</button>
    </form>
</div>
@endsection
