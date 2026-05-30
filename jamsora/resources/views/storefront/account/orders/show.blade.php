@extends('layouts.storefront')
@section('title', 'Order '.$order->order_number)

@section('content')
<div class="max-w-site mx-auto px-4 lg:px-8 py-16">
    <a href="{{ route('account.orders.index') }}" class="text-sm text-jamsora-gold uppercase tracking-widest">← Orders</a>
    <h1 class="font-display text-3xl mt-4 mb-2">{{ $order->order_number }}</h1>
    <p class="text-sm text-jamsora-muted mb-8">{{ $order->created_at->format('F j, Y g:i A') }} · {{ ucfirst($order->status) }}</p>

    <div class="grid lg:grid-cols-2 gap-10">
        <div class="border border-jamsora-border p-6">
            <h2 class="font-display text-xl mb-4">Items</h2>
            <ul class="space-y-4 text-sm">
                @foreach($order->items as $item)
                    <li>
                        <p class="font-medium">{{ $item->product_name }} × {{ $item->quantity }}</p>
                        <p class="text-jamsora-muted">${{ number_format($item->total, 2) }}</p>
                        @if($item->hasIgi())
                            <p class="text-xs text-jamsora-gold mt-1">IGI Certification: Yes (+${{ number_format($item->igiFee(), 2) }}) · +{{ config('jamsora.igi.extra_delivery_days', 7) }} days lead time</p>
                        @else
                            <p class="text-xs text-jamsora-muted mt-1">IGI Certification: No</p>
                        @endif
                        @if($item->product?->certification)
                            <p class="text-xs mt-2">
                                Certificate: {{ $item->product->certification->name }}
                                ({{ $item->product->certification->certificate_number }})
                            </p>
                        @endif
                    </li>
                @endforeach
            </ul>
            <dl class="mt-6 pt-6 border-t border-jamsora-border text-sm space-y-2">
                <div class="flex justify-between"><dt>Subtotal</dt><dd>${{ number_format($order->subtotal, 2) }}</dd></div>
                @if($order->igi_total > 0)
                    <div class="flex justify-between"><dt>IGI add-ons</dt><dd>${{ number_format($order->igi_total, 2) }}</dd></div>
                @endif
                <div class="flex justify-between font-medium text-lg"><dt>Total</dt><dd>${{ number_format($order->total, 2) }}</dd></div>
                @if($order->deliveryEstimate())
                    <div class="flex justify-between text-jamsora-muted"><dt>Delivery estimate</dt><dd>{{ $order->deliveryEstimate() }}</dd></div>
                @endif
            </dl>
        </div>
        @php $ship = $order->shippingAddress(); @endphp
        @if($ship)
            <div class="border border-jamsora-border p-6 text-sm">
                <h2 class="font-display text-xl mb-4">Shipping</h2>
                <p>{{ $ship->name }}</p>
                <p>{{ $ship->address_line1 }}</p>
                <p>{{ $ship->city }}, {{ $ship->state }} {{ $ship->zip }}</p>
                <p>{{ $ship->country }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
