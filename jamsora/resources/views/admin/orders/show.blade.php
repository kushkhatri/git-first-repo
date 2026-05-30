@extends('layouts.admin')
@section('heading', 'Order '.$order->order_number)
@section('content')
<div class="grid lg:grid-cols-2 gap-8">
    <div class="bg-white p-6 rounded-lg border">
        <h2 class="font-semibold mb-4">Items</h2>
        <ul class="space-y-3 text-sm">
            @foreach($order->items as $item)
                <li class="border-b pb-3">
                    <div class="flex justify-between"><span>{{ $item->product_name }} × {{ $item->quantity }}</span><span>${{ number_format($item->total, 2) }}</span></div>
                    @if($item->hasIgi())
                        <p class="text-xs text-amber-800 mt-1">IGI Certification: Yes · Fee ${{ number_format($item->igiFee(), 2) }} · +{{ config('jamsora.igi.extra_delivery_days', 7) }} days</p>
                    @else
                        <p class="text-xs text-stone-500 mt-1">IGI Certification: No</p>
                    @endif
                </li>
            @endforeach
        </ul>
        <dl class="mt-4 text-sm space-y-1">
            <div class="flex justify-between"><dt>Subtotal</dt><dd>${{ number_format($order->subtotal, 2) }}</dd></div>
            @if($order->igi_total > 0)
                <div class="flex justify-between"><dt>IGI total</dt><dd>${{ number_format($order->igi_total, 2) }}</dd></div>
            @endif
            <div class="flex justify-between font-semibold text-lg"><dt>Order total</dt><dd>${{ number_format($order->total, 2) }}</dd></div>
            @if($order->deliveryEstimate())
                <div class="flex justify-between text-stone-600"><dt>Delivery estimate</dt><dd>{{ $order->deliveryEstimate() }}</dd></div>
            @endif
        </dl>
    </div>
    <div class="space-y-6">
        <div class="bg-white p-6 rounded-lg border">
            <h2 class="font-semibold mb-4">Update status</h2>
            <form method="post" action="{{ route('admin.orders.status', $order) }}">@csrf @method('PATCH')
                <select name="status" class="w-full rounded border-stone-300 mb-3">
                    @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                        <option value="{{ $s }}" @selected($order->status===$s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button class="bg-amber-800 text-white px-4 py-2 rounded-lg text-sm">Update</button>
            </form>
        </div>
        @foreach($order->addresses as $addr)
            <div class="bg-white p-6 rounded-lg border text-sm">
                <h2 class="font-semibold mb-2 capitalize">{{ $addr->type }} address</h2>
                <p>{{ $addr->name }}</p>
                <p>{{ $addr->address_line1 }}</p>
                <p>{{ $addr->city }}, {{ $addr->state }} {{ $addr->zip }}</p>
                <p>{{ $addr->country }}</p>
            </div>
        @endforeach
    </div>
</div>
@endsection
