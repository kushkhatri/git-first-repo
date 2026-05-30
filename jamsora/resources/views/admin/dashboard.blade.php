@extends('layouts.admin')
@section('heading', 'Dashboard')
@section('content')
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="bg-white p-6 rounded-lg border"><p class="text-stone-500 text-sm">Products</p><p class="text-3xl font-semibold">{{ $productCount }}</p></div>
    <div class="bg-white p-6 rounded-lg border"><p class="text-stone-500 text-sm">Orders</p><p class="text-3xl font-semibold">{{ $orderCount }}</p></div>
    <div class="bg-white p-6 rounded-lg border"><p class="text-stone-500 text-sm">Customers</p><p class="text-3xl font-semibold">{{ $customerCount }}</p></div>
    <div class="bg-white p-6 rounded-lg border"><p class="text-stone-500 text-sm">Revenue</p><p class="text-3xl font-semibold">${{ number_format($revenue, 2) }}</p></div>
</div>
<h2 class="text-lg font-semibold mb-4">Recent orders</h2>
<div class="bg-white rounded-lg border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 text-left"><tr><th class="p-3">Order</th><th class="p-3">Customer</th><th class="p-3">Total</th><th class="p-3">Status</th></tr></thead>
        <tbody>
            @foreach($recentOrders as $order)
                <tr class="border-t"><td class="p-3"><a href="{{ route('admin.orders.show', $order) }}" class="text-amber-800">{{ $order->order_number }}</a></td>
                    <td class="p-3">{{ $order->user?->name ?? $order->guest_email ?? 'Guest' }}</td>
                    <td class="p-3">${{ number_format($order->total, 2) }}</td>
                    <td class="p-3 capitalize">{{ $order->status }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
