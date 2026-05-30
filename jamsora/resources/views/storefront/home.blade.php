@extends('layouts.storefront')
@section('title', $siteName)

@section('content')
@php
    $heroSlides = $slider?->slides?->isNotEmpty()
        ? $slider->slides
        : collect([
            (object)['title' => 'Elegance In', 'subtitle' => 'Minimalist Design', 'link' => route('shop.index'), 'image' => 'https://shop.jamsora.com/wp-content/uploads/1.jpg'],
            (object)['title' => 'Gemstone Set', 'subtitle' => 'For Special Events', 'link' => route('shop.index'), 'image' => 'https://shop.jamsora.com/wp-content/uploads/2.jpg'],
            (object)['title' => 'The History Of', 'subtitle' => 'Fine Gemstones', 'link' => route('shop.index'), 'image' => 'https://shop.jamsora.com/wp-content/uploads/3.jpg'],
        ]);
@endphp

{{-- Hero slider (Dagas Home 3 style) --}}
<section class="relative overflow-hidden bg-jamsora-cream" x-data="{
    active: 0,
    total: {{ $heroSlides->count() }},
    autoplay: null,
    init() {
        this.autoplay = setInterval(() => { this.active = (this.active + 1) % this.total }, 6000);
    },
    go(i) { this.active = i; clearInterval(this.autoplay); this.init(); }
}">
    @foreach($heroSlides as $index => $slide)
        <div x-show="active === {{ $index }}" x-cloak
             x-transition:enter="transition ease-out duration-700"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="absolute inset-0">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $slide->image ?? '' }}')"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-white/95 via-white/80 to-white/30"></div>
        </div>
    @endforeach

    <div class="relative max-w-site mx-auto px-4 lg:px-8 min-h-[520px] md:min-h-[600px] flex items-center">
        @foreach($heroSlides as $index => $slide)
            <div x-show="active === {{ $index }}" x-cloak class="max-w-xl py-16 md:py-24">
                <p class="subheading-dagas mb-4">You bring grace & cultivated taste</p>
                <h1 class="heading-dagas mb-2">
                    {{ $slide->title ?? 'Elegance In' }}<br>
                    <span class="italic text-jamsora-gold-dark">{{ $slide->subtitle ?? 'Minimalist Design' }}</span>
                </h1>
                <p class="text-jamsora-muted text-sm md:text-base leading-relaxed mt-6 mb-10 max-w-md">
                    Certified untreated gemstones — sapphires, emeralds, and rare finds curated for the modern collector.
                </p>
                <a href="{{ $slide->link ?? route('shop.index') }}" class="btn-dagas">Shop now</a>
            </div>
        @endforeach
    </div>

    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-2 z-10">
        @foreach($heroSlides as $index => $slide)
            <button type="button" @click="go({{ $index }})"
                    :class="active === {{ $index }} ? 'bg-jamsora-gold w-8' : 'bg-jamsora-border w-2'"
                    class="h-2 rounded-full transition-all duration-300" aria-label="Slide {{ $index + 1 }}"></button>
        @endforeach
    </div>
</section>

{{-- Service icons --}}
<section class="border-y border-jamsora-border bg-white">
    <div class="max-w-site mx-auto px-4 lg:px-8 py-12 grid grid-cols-2 lg:grid-cols-4 gap-8">
        @foreach([
            ['title' => 'Shipping & delivery', 'text' => 'Insured worldwide shipping', 'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4'],
            ['title' => 'Returns & exchange', 'text' => 'Hassle-free return policy', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
            ['title' => 'Genuine product', 'text' => 'Certified authentic gems', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            ['title' => 'Secure shopping', 'text' => 'Encrypted checkout', 'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
        ] as $service)
            <div class="text-center px-2">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full border border-jamsora-border text-jamsora-gold mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $service['icon'] }}"/></svg>
                </div>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-jamsora-ink mb-1">{{ $service['title'] }}</h3>
                <p class="text-xs text-jamsora-muted">{{ $service['text'] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- Uniqueness banner --}}
<section class="relative overflow-hidden">
    <div class="grid lg:grid-cols-2 min-h-[400px]">
        <div class="bg-jamsora-sand flex items-center p-10 lg:p-20 order-2 lg:order-1">
            <div>
                <p class="subheading-dagas mb-3">News & inspired</p>
                <h2 class="font-display text-3xl md:text-4xl text-jamsora-ink leading-tight mb-6">The uniqueness of<br>our collection</h2>
                <p class="text-jamsora-muted text-sm leading-relaxed mb-8 max-w-md">Shine and be confident with certified gemstones — each piece selected for clarity, color, and exceptional value.</p>
                <a href="{{ route('shop.index') }}" class="btn-dagas-outline">Discover now</a>
            </div>
        </div>
        <div class="bg-cover bg-center min-h-[280px] lg:min-h-full order-1 lg:order-2" style="background-image: url('https://shop.jamsora.com/wp-content/uploads/2.jpg')"></div>
    </div>
</section>

{{-- Featured products with tabs --}}
<section class="py-16 md:py-24 bg-white" x-data="{ tab: '{{ $tabCategories->first()?->slug ?? 'all' }}' }">
    <div class="max-w-site mx-auto px-4 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="font-display text-3xl md:text-4xl text-jamsora-ink">Featured Products</h2>
        </div>

        <div class="flex flex-wrap justify-center gap-6 md:gap-10 mb-12 text-xs uppercase tracking-[0.2em] border-b border-jamsora-border pb-4">
            <button type="button" @click="tab = 'all'" :class="tab === 'all' ? 'text-jamsora-gold border-b-2 border-jamsora-gold -mb-[17px] pb-4' : 'text-jamsora-muted hover:text-jamsora-ink'" class="transition">Best sellers</button>
            @foreach($tabCategories as $cat)
                <button type="button" @click="tab = '{{ $cat->slug }}'"
                        :class="tab === '{{ $cat->slug }}' ? 'text-jamsora-gold border-b-2 border-jamsora-gold -mb-[17px] pb-4' : 'text-jamsora-muted hover:text-jamsora-ink'"
                        class="transition">{{ $cat->name }}</button>
            @endforeach
        </div>

        <div x-show="tab === 'all'" class="grid grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
            @foreach($featured as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>

        @foreach($tabCategories as $cat)
            <div x-show="tab === '{{ $cat->slug }}'" x-cloak class="grid grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
                @forelse($productsByCategory[$cat->slug] ?? [] as $product)
                    <x-product-card :product="$product" />
                @empty
                    <p class="col-span-full text-center text-jamsora-muted py-12">No products in this category yet.</p>
                @endforelse
            </div>
        @endforeach

        <div class="text-center mt-12">
            <a href="{{ route('shop.index') }}" class="btn-dagas-outline">View all products</a>
        </div>
    </div>
</section>

{{-- Promo: store / convergence --}}
<section class="grid md:grid-cols-2">
    <div class="relative min-h-[320px] bg-cover bg-center flex items-center justify-center p-10" style="background-image: linear-gradient(rgba(0,0,0,.4), rgba(0,0,0,.4)), url('https://shop.jamsora.com/wp-content/uploads/3.jpg')">
        <div class="text-center text-white">
            <p class="text-xs uppercase tracking-[0.3em] mb-3 opacity-90">Visit us</p>
            <h3 class="font-display text-3xl mb-6">Explore our gemstone gallery</h3>
            <a href="{{ route('shop.index') }}" class="btn-dagas-gold">Find now</a>
        </div>
    </div>
    <div class="bg-jamsora-ink text-white flex items-center p-10 lg:p-16">
        <div>
            <p class="text-jamsora-gold text-xs uppercase tracking-[0.3em] mb-3">Convergence of beauty</p>
            <h3 class="font-display text-3xl md:text-4xl leading-tight mb-6">The centerpiece of your collection</h3>
            <p class="text-white/70 text-sm leading-relaxed mb-8">Visually impressive gemstones — certified, untreated, and ready to become yours.</p>
            <a href="{{ route('shop.index') }}" class="inline-block border border-white/40 text-white px-10 py-3.5 text-xs uppercase tracking-[0.2em] hover:bg-white hover:text-jamsora-ink transition">Shop now</a>
        </div>
    </div>
</section>

{{-- New arrivals --}}
<section class="py-16 md:py-24 bg-jamsora-cream">
    <div class="max-w-site mx-auto px-4 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-12">
            <div>
                <p class="subheading-dagas mb-2">Just in</p>
                <h2 class="font-display text-3xl md:text-4xl text-jamsora-ink">New Arrivals</h2>
            </div>
            <a href="{{ route('shop.index') }}" class="text-xs uppercase tracking-[0.2em] text-jamsora-gold hover:text-jamsora-gold-dark transition">View all →</a>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
            @foreach($newArrivals as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
</section>

{{-- Testimonials --}}
@if($testimonials->isNotEmpty())
<section class="py-16 md:py-24 bg-white border-t border-jamsora-border">
    <div class="max-w-site mx-auto px-4 lg:px-8">
        <div class="text-center mb-12">
            <p class="subheading-dagas mb-2">Testimonials</p>
            <h2 class="font-display text-3xl text-jamsora-ink">What our clients say</h2>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($testimonials as $t)
                <blockquote class="text-center p-6 border border-jamsora-border bg-jamsora-cream/50">
                    <div class="flex justify-center gap-0.5 text-jamsora-gold mb-4">
                        @for($i = 0; $i < ($t->rating ?? 5); $i++)
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-sm text-jamsora-muted leading-relaxed italic mb-4">"{{ Str::limit($t->content, 120) }}"</p>
                    <footer class="font-display text-lg text-jamsora-ink">{{ $t->name }}</footer>
                </blockquote>
            @endforeach
        </div>
    </div>
</section>
@else
<section class="py-16 md:py-24 bg-white border-t border-jamsora-border">
    <div class="max-w-site mx-auto px-4 lg:px-8">
        <div class="text-center mb-12">
            <p class="subheading-dagas mb-2">Testimonials</p>
            <h2 class="font-display text-3xl text-jamsora-ink">What our clients say</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            @foreach([
                ['name' => 'Linda Maria', 'quote' => 'Your commitment to quality and customer satisfaction has been evident in every interaction.'],
                ['name' => 'Ann Smith', 'quote' => 'A great company to buy from. Excellent quality products at good value.'],
                ['name' => 'Anana', 'quote' => '5-star rating 100%. So amazing and helpful. Stress-free and fun.'],
            ] as $t)
                <blockquote class="text-center p-8 border border-jamsora-border">
                    <p class="text-sm text-jamsora-muted leading-relaxed italic mb-4">"{{ $t['quote'] }}"</p>
                    <footer class="font-display text-lg text-jamsora-ink">{{ $t['name'] }}</footer>
                </blockquote>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
