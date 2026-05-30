@extends('layouts.storefront')
@section('title', $product->name)

@section('content')
<div class="bg-jamsora-cream border-b border-jamsora-border py-6">
    <div class="max-w-site mx-auto px-4 lg:px-8 text-xs uppercase tracking-widest text-jamsora-muted">
        <a href="{{ route('home') }}" class="hover:text-jamsora-gold">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ route('shop.index') }}" class="hover:text-jamsora-gold">Shop</a>
        @if($product->category)
            <span class="mx-2">/</span>
            <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="hover:text-jamsora-gold">{{ $product->category->name }}</a>
        @endif
    </div>
</div>

<div class="max-w-site mx-auto px-4 lg:px-8 py-12 md:py-16">
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16">
        <div class="product-thumb bg-jamsora-cream aspect-[4/5] overflow-hidden">
            @if($product->images->isNotEmpty())
                <div class="grid grid-cols-1 gap-2 h-full">
                    <img src="{{ $product->primaryImageUrl() }}" alt="{{ $product->name }}" class="w-full h-full object-cover" id="main-image">
                    @if($product->images->count() > 1)
                        <div class="flex gap-2 p-2 bg-white">
                            @foreach($product->images->take(4) as $img)
                                <button type="button" onclick="document.getElementById('main-image').src='{{ $img->path }}'"
                                        class="w-16 h-16 flex-shrink-0 border border-jamsora-border overflow-hidden hover:border-jamsora-gold">
                                    <img src="{{ $img->path }}" alt="" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="lg:pt-4">
            @if($product->category)
                <p class="subheading-dagas mb-3">{{ $product->category->name }}</p>
            @endif
            <h1 class="font-display text-3xl md:text-4xl lg:text-5xl text-jamsora-ink leading-tight mb-4">{{ $product->name }}</h1>
            <p class="text-xs uppercase tracking-widest text-jamsora-subtle mb-6">SKU: {{ $product->sku }}</p>

            <div class="flex items-baseline gap-4 mb-8 pb-8 border-b border-jamsora-border">
                @if($product->isOnSale())
                    <span class="font-display text-4xl text-jamsora-gold-dark">${{ number_format($product->sale_price, 2) }}</span>
                    <span class="text-xl text-jamsora-subtle line-through">${{ number_format($product->price, 2) }}</span>
                    <span class="bg-jamsora-gold text-white text-[10px] uppercase tracking-wider px-2 py-1">Sale</span>
                @else
                    <span class="font-display text-4xl text-jamsora-ink">${{ number_format($product->price, 2) }}</span>
                @endif
            </div>

            @if($product->gem_attributes)
                <table class="w-full text-sm mb-8">
                    @foreach($product->gem_attributes as $key => $val)
                        @if($val)
                            <tr class="border-b border-jamsora-border">
                                <td class="py-3 text-jamsora-muted uppercase tracking-wider text-xs w-1/3">{{ $key }}</td>
                                <td class="py-3 font-medium">{{ $val }}</td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            @endif

            @if($product->short_description)
                <div class="text-jamsora-muted text-sm leading-relaxed mb-8">{!! nl2br(e($product->short_description)) !!}</div>
            @endif

            <form action="{{ route('cart.store') }}" method="post" class="flex flex-wrap gap-4 mb-8">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="flex border border-jamsora-border">
                    <label class="sr-only">Quantity</label>
                    <input type="number" name="quantity" value="1" min="1" max="{{ max(1, $product->stock_qty) }}"
                           class="w-16 text-center border-0 focus:ring-0 text-sm">
                </div>
                <button type="submit" @disabled(!$product->inStock()) class="btn-dagas flex-1 min-w-[200px] disabled:opacity-40">
                    {{ $product->inStock() ? 'Add to cart' : 'Out of stock' }}
                </button>
            </form>

            <div class="grid grid-cols-2 gap-4 text-xs text-jamsora-muted uppercase tracking-wider">
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-jamsora-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg> Certified</span>
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-jamsora-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg> Insured ship</span>
            </div>
        </div>
    </div>

    @if($related->isNotEmpty())
        <section class="mt-20 pt-16 border-t border-jamsora-border">
            <h2 class="font-display text-3xl text-center mb-12">You may also like</h2>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
                @foreach($related as $p)
                    <x-product-card :product="$p" />
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
