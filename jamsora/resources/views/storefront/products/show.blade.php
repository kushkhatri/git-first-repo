@extends('layouts.storefront')
@section('title', $meta['title'] ?? $product->name)
@section('meta_description', $meta['description'] ?? '')

@section('content')
<x-seo-head :meta="$meta" :schemas="$schemas" />

@php
    $basePrice = $product->effective_price;
    $igiFee = $igiFee ?? config('jamsora.igi.fee', 100);
    $igiExtra = $igiExtraDays ?? config('jamsora.igi.extra_delivery_days', 7);
@endphp

<div class="bg-jamsora-cream border-b border-jamsora-border py-6">
    <div class="max-w-site mx-auto px-4 lg:px-8 text-xs uppercase tracking-widest text-jamsora-muted">
        <a href="{{ route('home') }}" class="hover:text-jamsora-gold">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ route('shop.index') }}" class="hover:text-jamsora-gold">Shop</a>
        @if($product->category)
            <span class="mx-2">/</span>
            <a href="{{ route('categories.show', $product->category->slug) }}" class="hover:text-jamsora-gold">{{ $product->category->name }}</a>
        @endif
    </div>
</div>

<div class="max-w-site mx-auto px-4 lg:px-8 py-12 md:py-16" x-data="{
    igi: false,
    base: {{ $basePrice }},
    fee: {{ $igiFee }},
    extraDays: {{ $igiExtra }},
    minDays: {{ (int) $product->delivery_days_min }},
    maxDays: {{ (int) $product->delivery_days_max }},
    get total() { return this.base + (this.igi ? this.fee : 0); },
    get delivery() {
        const min = this.minDays + (this.igi ? this.extraDays : 0);
        const max = this.maxDays + (this.igi ? this.extraDays : 0);
        return min + '-' + max + ' Days';
    }
}">
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16">
        <div class="product-thumb bg-jamsora-cream aspect-[4/5] overflow-hidden relative group">
            @if($product->images->isNotEmpty())
                <img src="{{ $product->primaryImageUrl() }}" alt="{{ $product->name }}"
                     class="w-full h-full object-cover cursor-zoom-in transition duration-300 group-hover:scale-105"
                     id="main-image" onclick="this.classList.toggle('scale-150')">
                @if($product->images->count() > 1)
                    <div class="absolute bottom-0 left-0 right-0 flex gap-2 p-3 bg-white/90">
                        @foreach($product->images->take(5) as $img)
                            <button type="button" onclick="document.getElementById('main-image').src='{{ $img->path }}'"
                                    class="w-14 h-14 border border-jamsora-border overflow-hidden hover:border-jamsora-gold">
                                <img src="{{ $img->path }}" alt="" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>

        <div class="lg:pt-4">
            @if($product->category)
                <p class="subheading-dagas mb-3">{{ $product->category->name }}</p>
            @endif
            <h1 class="font-display text-3xl md:text-4xl lg:text-5xl text-jamsora-ink leading-tight mb-4">{{ $product->name }}</h1>
            <p class="text-xs uppercase tracking-widest text-jamsora-subtle mb-6">SKU: {{ $product->sku }}</p>

            <div class="flex items-baseline gap-4 mb-4 pb-6 border-b border-jamsora-border">
                <span class="font-display text-4xl text-jamsora-gold-dark" x-text="'$' + total.toFixed(2)"></span>
                @if($product->isOnSale())
                    <span class="text-xl text-jamsora-subtle line-through">${{ number_format($product->price, 2) }}</span>
                @endif
            </div>

            <p class="text-sm text-jamsora-muted mb-6">
                Estimated delivery: <strong x-text="delivery"></strong>
                <span class="block text-xs mt-1" x-show="igi">Includes +{{ $igiExtra }} days for IGI certification</span>
            </p>

            @php $specs = $product->displaySpecifications(); @endphp
            @if(!empty($specs))
                <table class="w-full text-sm mb-6">
                    @foreach($specs as $label => $val)
                        <tr class="border-b border-jamsora-border">
                            <td class="py-3 text-jamsora-muted uppercase tracking-wider text-xs w-1/3">{{ $label }}</td>
                            <td class="py-3 font-medium">{{ $val }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            @if($product->certification)
                <button type="button" @click="$dispatch('open-cert-modal')"
                        class="text-sm uppercase tracking-widest text-jamsora-gold border border-jamsora-gold px-4 py-2 mb-6 hover:bg-jamsora-gold hover:text-white transition">
                    View Certification
                </button>
            @endif

            <form action="{{ route('cart.store') }}" method="post" class="space-y-4 mb-8">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="igi_certification" :value="igi ? 1 : 0">

                @if($product->igi_available)
                    <label class="flex items-start gap-3 cursor-pointer border border-jamsora-border p-4">
                        <input type="checkbox" name="igi_certification_ui" x-model="igi" class="mt-1 rounded border-jamsora-border text-jamsora-gold focus:ring-jamsora-gold">
                        <span class="text-sm">
                            <strong>Add IGI Certification (+${{ number_format($igiFee, 0) }})</strong>
                            <span class="block text-jamsora-muted text-xs mt-1">Adds {{ $igiExtra }} business days to delivery</span>
                        </span>
                    </label>
                @endif

                <div class="flex flex-wrap gap-4">
                    <div class="flex border border-jamsora-border">
                        <input type="number" name="quantity" value="1" min="1" max="{{ max(1, $product->stock_qty) }}"
                               class="w-16 text-center border-0 focus:ring-0 text-sm">
                    </div>
                    <button type="submit" @disabled(!$product->inStock()) class="btn-dagas flex-1 min-w-[200px] disabled:opacity-40">
                        {{ $product->inStock() ? 'Add to cart' : 'Out of stock' }}
                    </button>
                </div>
            </form>

            @if($product->description)
                <div class="prose prose-sm text-jamsora-muted max-w-none mb-8">{!! nl2br(e($product->description)) !!}</div>
            @endif
        </div>
    </div>

    @if($product->seo_content)
        <section class="mt-16 pt-12 border-t border-jamsora-border prose prose-sm max-w-3xl mx-auto text-jamsora-muted">
            {!! $product->seo_content !!}
        </section>
    @endif

    @if($product->faqs->isNotEmpty())
        <section class="mt-16 max-w-3xl mx-auto" x-data="{ open: null }">
            <h2 class="font-display text-2xl mb-8 text-center">Product FAQs</h2>
            <div class="divide-y divide-jamsora-border">
                @foreach($product->faqs as $i => $faq)
                    <div class="py-4">
                        <button type="button" class="w-full text-left flex justify-between gap-4 text-sm font-medium"
                                @click="open = open === {{ $i }} ? null : {{ $i }}">
                            {{ $faq->question }}
                            <span x-text="open === {{ $i }} ? '−' : '+'"></span>
                        </button>
                        <p x-show="open === {{ $i }}" class="mt-3 text-sm text-jamsora-muted">{{ $faq->answer }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

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

@if($product->certification)
    <div x-data="{ open: false }" @open-cert-modal.window="open = true">
        <div x-show="open" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50" @keydown.escape.window="open = false">
            <div class="bg-white max-w-lg w-full p-8 relative" @click.outside="open = false">
                <button type="button" class="absolute top-4 right-4 text-2xl" @click="open = false">&times;</button>
                <h3 class="font-display text-2xl mb-4">{{ $product->certification->name }}</h3>
                <dl class="text-sm space-y-2 mb-6">
                    <div><dt class="text-jamsora-muted inline">Agency:</dt> <dd class="inline">{{ $product->certification->agency }}</dd></div>
                    <div><dt class="text-jamsora-muted inline">Certificate #:</dt> <dd class="inline">{{ $product->certification->certificate_number }}</dd></div>
                    @if($product->certification->verification_info)
                        <div><dt class="text-jamsora-muted">Verification:</dt> <dd>{{ $product->certification->verification_info }}</dd></div>
                    @endif
                </dl>
                @if($product->certification->image_path)
                    <img src="{{ $product->certification->image_path }}" alt="Certificate" class="w-full border border-jamsora-border mb-4">
                @endif
                @if($product->certification->pdf_path)
                    <a href="{{ $product->certification->pdf_path }}" target="_blank" class="text-jamsora-gold text-sm uppercase tracking-widest">Download PDF</a>
                @endif
            </div>
        </div>
    </div>
@endif
@endsection
