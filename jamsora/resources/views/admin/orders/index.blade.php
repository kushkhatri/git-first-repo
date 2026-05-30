@extends('layouts.admin')
@section('heading', 'Orders')
@section('content')
<div class="bg-white rounded-lg border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50"><tr><th class="p-3 text-left">Order</th><th class="p-3">Customer</th><th class="p-3">Total</th><th class="p-3">Status</th><th class="p-3">Date</th></tr></thead>
        <tbody>
            @foreach($orders as $order)
                <tr class="border-t">
                    <td class="p-3"><a href="{{ route('admin.orders.show', $order) }}" class="text-amber-800 font-medium">{{ $order->order_number }}</a></td>
                    <td class="p-3">{{ $order->user?->name ?? $order->guest_email ?? 'Guest' }}</td>
                    <td class="p-3">${{ number_format($order->total, 2) }}</td>
                    <td class="p-3 capitalize">{{ $order->status }}</td>
                    <td class="p-3 text-stone-500">{{ $order->created_at->format('M j, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $orders->links() }}
@endsection
