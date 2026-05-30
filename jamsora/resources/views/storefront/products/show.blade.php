@extends('layouts.storefront')
@section('title', $product->name)
@section('content')
<div class="max-w-7xl mx-auto px-4 py-12">
    <div class="grid lg:grid-cols-2 gap-12">
        <div class="bg-stone-100 rounded-lg aspect-square overflow-hidden">
            @if($product->primaryImageUrl())
                <img src="{{ $product->primaryImageUrl() }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
            @endif
        </div>
        <div>
            @if($product->category)<p class="text-amber-800 text-sm uppercase tracking-wider">{{ $product->category->name }}</p>@endif
            <h1 class="font-serif text-4xl mt-2 mb-4">{{ $product->name }}</h1>
            <p class="text-sm text-stone-500 mb-4">SKU: {{ $product->sku }}</p>
            <div class="flex items-baseline gap-3 mb-6">
                @if($product->isOnSale())
                    <span class="text-2xl font-semibold text-amber-900">${{ number_format($product->sale_price, 2) }}</span>
                    <span class="text-stone-400 line-through">${{ number_format($product->price, 2) }}</span>
                @else
                    <span class="text-2xl font-semibold text-amber-900">${{ number_format($product->price, 2) }}</span>
                @endif
            </div>
            @if($product->gem_attributes)
                <dl class="grid grid-cols-2 gap-3 text-sm mb-6 bg-white p-4 rounded-lg border">
                    @foreach($product->gem_attributes as $key => $val)
                        @if($val)<div><dt class="text-stone-500 capitalize">{{ $key }}</dt><dd class="font-medium">{{ $val }}</dd></div>@endif
                    @endforeach
                </dl>
            @endif
            <div class="mb-8 text-stone-600">{!! nl2br(e($product->short_description)) !!}</div>
            <form action="{{ route('cart.store') }}" method="post" class="flex gap-3">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="number" name="quantity" value="1" min="1" max="{{ max(1, $product->stock_qty) }}" class="w-20 rounded-lg border-stone-300">
                <button type="submit" @disabled(!$product->inStock()) class="flex-1 bg-amber-800 hover:bg-amber-700 disabled:bg-stone-300 text-white py-3 rounded-full font-medium">
                    {{ $product->inStock() ? 'Add to cart' : 'Out of stock' }}
                </button>
            </form>
        </div>
    </div>
    @if($related->isNotEmpty())
        <section class="mt-20">
            <h2 class="font-serif text-2xl mb-6">You may also like</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($related as $p)<x-product-card :product="$p" />@endforeach
            </div>
        </section>
    @endif
</div>
@endsection
