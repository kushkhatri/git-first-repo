@extends('layouts.storefront')
@section('title', 'Order confirmed')
@section('content')
<div class="max-w-xl mx-auto px-4 py-20 text-center">
    <h1 class="font-serif text-4xl text-emerald-800 mb-4">Thank you!</h1>
    <p class="text-stone-600 mb-2">Your order <strong>{{ $order->order_number }}</strong> has been placed.</p>
    <p class="text-stone-500 text-sm mb-8">Total: ${{ number_format($order->total, 2) }}</p>
    <a href="{{ route('shop.index') }}" class="text-amber-800 font-medium">Continue shopping</a>
</div>
@endsection
