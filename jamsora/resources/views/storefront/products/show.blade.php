@extends('layouts.storefront')
@section('title', $product->name)
@section('content')
<div class="max-w-7xl mx-auto px-4 py-16">
    <div class="grid lg:grid-cols-2 gap-14">
        <div class="bg-jamsora-cream border border-jamsora-border aspect-square overflow-hidden">
            @if($product->primaryImageUrl())
                <img src="{{ $product->primaryImageUrl() }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
            @endif
        </div>
        <div>
            @if($product->category)
                <p class="text-[10px] uppercase tracking-[0.3em] text-jamsora-champagne mb-3">{{ $product->category->name }}</p>
            @endif
            <h1 class="font-display text-3xl md:text-4xl text-jamsora-ink mb-4">{{ $product->name }}</h1>
            <p class="text-xs uppercase tracking-widest text-jamsora-muted mb-6">SKU {{ $product->sku }}</p>
            <div class="flex items-baseline gap-3 mb-8 pb-8 border-b border-jamsora-border">
                @if($product->isOnSale())
                    <span class="text-3xl font-light text-jamsora-ink">${{ number_format($product->sale_price, 2) }}</span>
                    <span class="text-jamsora-subtle line-through">${{ number_format($product->price, 2) }}</span>
                @else
                    <span class="text-3xl font-light text-jamsora-ink">${{ number_format($product->price, 2) }}</span>
                @endif
            </div>
            @if($product->gem_attributes)
                <dl class="grid grid-cols-2 gap-4 text-sm mb-8">
                    @foreach($product->gem_attributes as $key => $val)
                        @if($val)
                            <div class="border-l-2 border-jamsora-champagne pl-3">
                                <dt class="text-[10px] uppercase tracking-widest text-jamsora-muted">{{ $key }}</dt>
                                <dd class="font-medium text-jamsora-ink mt-0.5">{{ $val }}</dd>
                            </div>
                        @endif
                    @endforeach
                </dl>
            @endif
            @if($product->short_description)
                <div class="text-jamsora-muted text-sm leading-relaxed mb-8">{!! nl2br(e($product->short_description)) !!}</div>
            @endif
            <form action="{{ route('cart.store') }}" method="post" class="flex flex-wrap gap-3">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="number" name="quantity" value="1" min="1" max="{{ max(1, $product->stock_qty) }}" class="input-field w-20">
                <button type="submit" @disabled(!$product->inStock()) class="btn-primary flex-1 min-w-[200px] disabled:opacity-40">
                    {{ $product->inStock() ? 'Add to cart' : 'Out of stock' }}
                </button>
            </form>
        </div>
    </div>
    @if($related->isNotEmpty())
        <section class="mt-24 pt-16 border-t border-jamsora-border">
            <h2 class="section-title text-2xl mb-8">You may also like</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($related as $p)<x-product-card :product="$p" />@endforeach
            </div>
        </section>
    @endif
</div>
@endsection
