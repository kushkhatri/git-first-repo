@extends('layouts.storefront')
@section('title', $meta['title'] ?? $category->name)
@section('meta_description', $meta['description'] ?? '')

@section('content')
<x-seo-head :meta="$meta" :schemas="$schemas" />

<div class="bg-jamsora-cream border-b border-jamsora-border py-6">
    <div class="max-w-site mx-auto px-4 lg:px-8 text-xs uppercase tracking-widest text-jamsora-muted">
        <a href="{{ route('home') }}" class="hover:text-jamsora-gold">Home</a>
        <span class="mx-2">/</span>
        <span>{{ $category->name }}</span>
    </div>
</div>

<section class="max-w-site mx-auto px-4 lg:px-8 py-12 md:py-16">
    <header class="text-center max-w-3xl mx-auto mb-12">
        <p class="subheading-dagas mb-3">Gemstone Collection</p>
        <h1 class="font-display text-4xl md:text-5xl text-jamsora-ink mb-4">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-jamsora-muted leading-relaxed">{{ $category->description }}</p>
        @endif
    </header>

    <form method="get" class="flex flex-wrap gap-4 items-end mb-10 pb-8 border-b border-jamsora-border">
        @if($shapes->isNotEmpty())
            <div>
                <label class="block text-xs uppercase tracking-widest text-jamsora-muted mb-2">Shape</label>
                <select name="shape" class="border border-jamsora-border text-sm px-3 py-2" onchange="this.form.submit()">
                    <option value="">All</option>
                    @foreach($shapes as $shape)
                        <option value="{{ $shape }}" @selected(request('shape') === $shape)>{{ $shape }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div>
            <label class="block text-xs uppercase tracking-widest text-jamsora-muted mb-2">Sort</label>
            <select name="sort" class="border border-jamsora-border text-sm px-3 py-2" onchange="this.form.submit()">
                <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest</option>
                <option value="price_asc" @selected(request('sort') === 'price_asc')>Price: Low to High</option>
                <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: High to Low</option>
                <option value="name" @selected(request('sort') === 'name')>Name</option>
            </select>
        </div>
    </form>

    @if($products->isEmpty())
        <p class="text-center text-jamsora-muted py-16">No products in this category yet. Check back soon.</p>
    @else
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8 mb-12">
            @foreach($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
        {{ $products->links() }}
    @endif
</section>

@if($category->seo_content)
    <section class="bg-jamsora-cream py-16">
        <div class="max-w-3xl mx-auto px-4 prose prose-sm text-jamsora-muted">
            {!! $category->seo_content !!}
        </div>
    </section>
@endif

@php $sections = [
    'what_is' => 'What is '.$category->name.'?',
    'history' => 'History',
    'benefits' => 'Benefits',
    'uses' => 'Uses',
    'quality_factors' => 'Quality Factors',
    'care' => 'Care Instructions',
    'buying_guide' => 'Buying Guide',
]; @endphp
<section class="max-w-site mx-auto px-4 lg:px-8 py-16 border-t border-jamsora-border">
    <h2 class="font-display text-3xl text-center mb-12">About {{ $category->name }}</h2>
    <div class="grid md:grid-cols-2 gap-10">
        @foreach($sections as $key => $label)
            @if($category->stoneSection($key))
                <div>
                    <h3 class="font-display text-xl mb-3">{{ $label }}</h3>
                    <p class="text-sm text-jamsora-muted leading-relaxed">{{ $category->stoneSection($key) }}</p>
                </div>
            @endif
        @endforeach
    </div>
</section>

@if($category->faqs->isNotEmpty())
    <section class="bg-white py-16 border-t border-jamsora-border" x-data="{ open: null }">
        <div class="max-w-3xl mx-auto px-4">
            <h2 class="font-display text-3xl text-center mb-10">Frequently Asked Questions</h2>
            <div class="divide-y divide-jamsora-border">
                @foreach($category->faqs as $i => $faq)
                    <div class="py-4">
                        <button type="button" class="w-full text-left flex justify-between gap-4 font-medium"
                                @click="open = open === {{ $i }} ? null : {{ $i }}">
                            <span>{{ $faq->question }}</span>
                            <span x-text="open === {{ $i }} ? '−' : '+'"></span>
                        </button>
                        <div x-show="open === {{ $i }}" x-collapse class="mt-3 text-sm text-jamsora-muted leading-relaxed">
                            {{ $faq->answer }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection
