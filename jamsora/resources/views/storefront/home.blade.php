@extends('layouts.storefront')
@section('title', $siteName)
@section('content')
<section class="relative bg-jamsora-ink text-white overflow-hidden">
    <div class="absolute inset-0 opacity-[0.03] bg-[radial-gradient(circle_at_30%_20%,#c5a572_0%,transparent_50%)]"></div>
    <div class="max-w-7xl mx-auto px-4 py-24 md:py-32 lg:py-40 relative">
        <div class="max-w-2xl">
            <p class="text-jamsora-champagne text-xs uppercase tracking-[0.35em] mb-6">{{ $tagline }}</p>
            <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-medium leading-[1.1] mb-8">Exceptional gemstones,<br><span class="text-white/80">curated for you</span></h1>
            <p class="text-white/60 text-sm md:text-base leading-relaxed mb-10 max-w-lg">Certified sapphires, emeralds, and rare gems — the same quality you expect from Jamsora, with a refined shopping experience.</p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('shop.index') }}" class="btn-primary bg-white text-jamsora-ink hover:bg-jamsora-cream">Explore collection</a>
                <a href="{{ route('pages.show', 'price-match-guarantee') }}" class="btn-outline border-white/40 text-white hover:bg-white hover:text-jamsora-ink">Price match</a>
            </div>
        </div>
    </div>
    <div class="h-px bg-gradient-to-r from-transparent via-jamsora-champagne/50 to-transparent"></div>
</section>

@if($categories->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 py-20">
    <div class="text-center mb-12">
        <p class="text-xs uppercase tracking-[0.35em] text-jamsora-champagne mb-2">Categories</p>
        <h2 class="section-title">Shop by gemstone</h2>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
        @foreach($categories as $category)
            <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
               class="text-center py-6 px-3 border border-jamsora-border bg-white hover:border-jamsora-ink hover:shadow-soft transition group">
                <span class="text-xs uppercase tracking-[0.2em] text-jamsora-muted group-hover:text-jamsora-ink transition">{{ $category->name }}</span>
            </a>
        @endforeach
    </div>
</section>
@endif

<section class="bg-jamsora-cream border-y border-jamsora-border">
    <div class="max-w-7xl mx-auto px-4 py-20">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-12">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-jamsora-champagne mb-2">Featured</p>
                <h2 class="section-title">Selected gemstones</h2>
            </div>
            <a href="{{ route('shop.index') }}" class="text-xs uppercase tracking-[0.2em] text-jamsora-ink hover:text-jamsora-champagne transition">View all →</a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($featured as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
</section>
@endsection
