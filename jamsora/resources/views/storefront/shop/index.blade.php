@extends('layouts.storefront')
@section('title', 'Shop')

@section('content')
{{-- Page hero --}}
<section class="bg-jamsora-cream border-b border-jamsora-border py-14 md:py-20">
    <div class="max-w-site mx-auto px-4 lg:px-8 text-center">
        <p class="subheading-dagas mb-3">Collection</p>
        <h1 class="font-display text-4xl md:text-5xl text-jamsora-ink">Shop Gemstones</h1>
        @if(request('category'))
            <p class="text-jamsora-muted mt-3 text-sm capitalize">{{ str_replace('-', ' ', request('category')) }}</p>
        @endif
    </div>
</section>

<div class="max-w-site mx-auto px-4 lg:px-8 py-12 md:py-16">
    <div class="flex flex-col lg:flex-row gap-10">
        <aside class="lg:w-56 flex-shrink-0">
            <h3 class="text-xs uppercase tracking-[0.2em] text-jamsora-ink font-semibold mb-5">Categories</h3>
            <ul class="space-y-3 text-sm">
                <li>
                    <a href="{{ route('shop.index') }}" class="{{ !request('category') ? 'text-jamsora-gold font-medium' : 'text-jamsora-muted hover:text-jamsora-gold' }} transition">All products</a>
                </li>
                @foreach($categories as $cat)
                    <li>
                        <a href="{{ route('shop.index', ['category' => $cat->slug]) }}"
                           class="{{ request('category') === $cat->slug ? 'text-jamsora-gold font-medium' : 'text-jamsora-muted hover:text-jamsora-gold' }} transition">
                            {{ $cat->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </aside>

        <div class="flex-1">
            <div class="flex flex-col sm:flex-row gap-4 mb-10 pb-6 border-b border-jamsora-border">
                <form class="flex flex-1 gap-3" method="get">
                    @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search…" class="input-dagas flex-1 border border-jamsora-border px-4 py-2.5 bg-white">
                    <button type="submit" class="btn-dagas py-2.5 px-6">Search</button>
                </form>
                <form method="get" class="flex items-center">
                    @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                    @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                    <select name="sort" class="border border-jamsora-border text-sm px-4 py-2.5 bg-white uppercase tracking-wider text-xs" onchange="this.form.submit()">
                        <option value="">Sort: Default</option>
                        <option value="price_asc" @selected(request('sort')==='price_asc')>Price ↑</option>
                        <option value="price_desc" @selected(request('sort')==='price_desc')>Price ↓</option>
                    </select>
                </form>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                @forelse($products as $product)
                    <x-product-card :product="$product" />
                @empty
                    <p class="col-span-full text-center text-jamsora-muted py-16 font-display text-xl">No products found.</p>
                @endforelse
            </div>

            <div class="mt-14 flex justify-center">{{ $products->links() }}</div>
        </div>
    </div>
</div>
@endsection
