@extends('layouts.admin')
@section('heading', 'Products')
@section('content')
<div class="flex justify-between mb-6">
    <p class="text-stone-500 text-sm">Manage gemstone catalog</p>
    <a href="{{ route('admin.products.create') }}" class="bg-amber-800 text-white px-4 py-2 rounded-lg text-sm">Add product</a>
</div>
<div class="bg-white rounded-lg border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50"><tr><th class="p-3 text-left">Name</th><th class="p-3">SKU</th><th class="p-3">Price</th><th class="p-3">Status</th><th class="p-3"></th></tr></thead>
        <tbody>
            @foreach($products as $product)
                <tr class="border-t">
                    <td class="p-3 font-medium">{{ $product->name }}</td>
                    <td class="p-3 text-stone-500">{{ $product->sku }}</td>
                    <td class="p-3">${{ number_format($product->effective_price, 2) }}</td>
                    <td class="p-3 capitalize">{{ $product->status }}</td>
                    <td class="p-3 text-right space-x-2">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-amber-800">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="post" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-red-600">Delete</button></form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
