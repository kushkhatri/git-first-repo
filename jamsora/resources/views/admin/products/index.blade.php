@extends('layouts.admin')
@section('heading', 'Products')
@section('content')
@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 text-sm">{{ session('error') }}</div>
@endif
<div class="flex flex-wrap justify-between items-center gap-4 mb-8">
    <p class="text-sm text-jamsora-muted">Manage catalog or bulk-import from CSV (WooCommerce / Google Sheets).</p>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.products.import') }}" class="text-xs uppercase tracking-widest border border-jamsora-border bg-white px-4 py-2 hover:border-jamsora-gold transition">
            Import CSV
        </a>
        <a href="{{ route('admin.products.create') }}" class="btn-primary text-xs py-2 px-4">Add product</a>
    </div>
</div>
<div class="bg-white border border-jamsora-border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-jamsora-cream text-left text-[10px] uppercase tracking-widest text-jamsora-muted">
            <tr><th class="p-4">Name</th><th class="p-4">SKU</th><th class="p-4">Category</th><th class="p-4">Price</th><th class="p-4">Status</th><th class="p-4"></th></tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr class="border-t border-jamsora-border">
                    <td class="p-4 font-medium text-jamsora-ink">{{ $product->name }}</td>
                    <td class="p-4 text-jamsora-muted">{{ $product->sku }}</td>
                    <td class="p-4 text-jamsora-muted">{{ $product->category?->name ?? '—' }}</td>
                    <td class="p-4">${{ number_format($product->effective_price, 2) }}</td>
                    <td class="p-4 capitalize text-jamsora-muted">{{ $product->status }}</td>
                    <td class="p-4 text-right space-x-3">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-xs uppercase tracking-widest text-jamsora-champagne-dark">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="post" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-xs uppercase tracking-widest text-red-800">Delete</button></form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $products->links() }}</div>
@endsection
