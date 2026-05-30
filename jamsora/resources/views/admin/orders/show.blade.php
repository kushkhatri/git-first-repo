@extends('layouts.admin')
@section('heading', 'Order '.$order->order_number)
@section('content')
<div class="grid lg:grid-cols-2 gap-8">
    <div class="bg-white p-6 rounded-lg border">
        <h2 class="font-semibold mb-4">Items</h2>
        <ul class="space-y-2 text-sm">
            @foreach($order->items as $item)
                <li class="flex justify-between"><span>{{ $item->product_name }} × {{ $item->quantity }}</span><span>${{ number_format($item->total, 2) }}</span></li>
            @endforeach
        </ul>
        <p class="mt-4 font-semibold">Total: ${{ number_format($order->total, 2) }}</p>
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
