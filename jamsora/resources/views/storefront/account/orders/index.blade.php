@extends('layouts.storefront')
@section('title', 'My Orders')

@section('content')
<div class="max-w-site mx-auto px-4 lg:px-8 py-16">
    <h1 class="font-display text-3xl mb-8">My Orders</h1>
    @if($orders->isEmpty())
        <p class="text-jamsora-muted">You have no orders yet.</p>
        <a href="{{ route('shop.index') }}" class="btn-dagas inline-block mt-6">Shop gemstones</a>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <a href="{{ route('account.orders.show', $order) }}" class="block border border-jamsora-border p-6 hover:border-jamsora-gold transition">
                    <div class="flex flex-wrap justify-between gap-4">
                        <div>
                            <p class="font-medium">{{ $order->order_number }}</p>
                            <p class="text-sm text-jamsora-muted">{{ $order->created_at->format('M j, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-display text-xl">${{ number_format($order->total, 2) }}</p>
                            <p class="text-xs uppercase tracking-widest text-jamsora-muted">{{ ucfirst($order->status) }}</p>
                            @if($order->deliveryEstimate())
                                <p class="text-xs text-jamsora-muted mt-1">Delivery: {{ $order->deliveryEstimate() }}</p>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-8">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
