@extends('layouts.storefront')
@section('title', 'Order confirmed')

@section('content')
<div class="max-w-xl mx-auto px-4 py-24 text-center">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full border-2 border-jamsora-gold text-jamsora-gold mb-8">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    </div>
    <p class="subheading-dagas mb-3">Thank you</p>
    <h1 class="font-display text-4xl mb-4">Order confirmed</h1>
    <p class="text-jamsora-muted mb-2">Order number <strong class="text-jamsora-ink">{{ $order->order_number }}</strong></p>
    <p class="text-jamsora-muted text-sm mb-10">Total: ${{ number_format($order->total, 2) }}</p>
    <a href="{{ route('shop.index') }}" class="btn-dagas">Continue shopping</a>
</div>
@endsection
