@extends('layouts.storefront')
@section('title', 'Order confirmed')
@section('content')
<div class="max-w-xl mx-auto px-4 py-24 text-center">
    <p class="text-xs uppercase tracking-[0.35em] text-jamsora-champagne mb-4">Confirmed</p>
    <h1 class="section-title mb-4">Thank you</h1>
    <p class="text-jamsora-muted mb-2">Order <strong class="text-jamsora-ink">{{ $order->order_number }}</strong></p>
    <p class="text-jamsora-muted text-sm mb-10">Total ${{ number_format($order->total, 2) }}</p>
    <a href="{{ route('shop.index') }}" class="btn-outline">Continue shopping</a>
</div>
@endsection
