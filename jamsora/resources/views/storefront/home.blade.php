@extends('layouts.storefront')
@section('title', $siteName)
@section('content')
<section class="relative bg-stone-900 text-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 py-24 md:py-32 grid md:grid-cols-2 gap-12 items-center">
        <div>
            <p class="text-amber-400 text-sm uppercase tracking-[0.2em] mb-4">{{ $tagline }}</p>
            <h1 class="font-serif text-4xl md:text-5xl leading-tight mb-6">Discover exceptional gemstones</h1>
            <p class="text-stone-300 mb-8 max-w-lg">Curated sapphires, emeralds, and rare gems — direct from Jamsora with certification and competitive pricing.</p>
            <a href="{{ route('shop.index') }}" class="inline-block bg-amber-700 hover:bg-amber-600 text-white px-8 py-3 rounded-full text-sm font-medium transition">Shop collection</a>
        </div>
        @if($slider?->slides->first())
            <img src="{{ $slider->slides->first()->image }}" alt="" class="rounded-lg shadow-2xl opacity-90">
        @endif
    </div>
</section>

@if($categories->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 py-16">
    <h2 class="font-serif text-3xl text-center mb-10">Shop by gemstone</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach($categories as $category)
            <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="text-center p-4 rounded-lg border border-stone-200 hover:border-amber-700 bg-white transition">
                <span class="font-medium text-stone-800">{{ $category->name }}</span>
            </a>
        @endforeach
    </div>
</section>
@endif

<section class="max-w-7xl mx-auto px-4 py-16">
    <div class="flex justify-between items-end mb-10">
        <h2 class="font-serif text-3xl">Featured gemstones</h2>
        <a href="{{ route('shop.index') }}" class="text-sm text-amber-800 font-medium">View all →</a>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($featured as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
</section>
@endsection
