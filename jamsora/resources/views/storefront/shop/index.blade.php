@extends('layouts.storefront')
@section('title', 'Shop')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-16">
    <p class="text-xs uppercase tracking-[0.35em] text-jamsora-champagne mb-2">Collection</p>
    <h1 class="section-title mb-10">Shop gemstones</h1>
    <div class="flex flex-col lg:flex-row gap-10">
        <aside class="lg:w-52 flex-shrink-0">
            <p class="text-[10px] uppercase tracking-[0.25em] text-jamsora-muted mb-4">Categories</p>
            <ul class="space-y-3 text-sm">
                <li><a href="{{ route('shop.index') }}" class="{{ !request('category') ? 'text-jamsora-ink font-medium' : 'text-jamsora-muted hover:text-jamsora-ink' }}">All</a></li>
                @foreach($categories as $cat)
                    <li><a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="{{ request('category') === $cat->slug ? 'text-jamsora-ink font-medium' : 'text-jamsora-muted hover:text-jamsora-ink' }}">{{ $cat->name }}</a></li>
                @endforeach
            </ul>
        </aside>
        <div class="flex-1">
            <form class="mb-8 flex flex-wrap gap-3" method="get">
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search gemstones…" class="input-field flex-1 min-w-[200px]">
                <select name="sort" class="input-field w-auto" onchange="this.form.submit()">
                    <option value="">Newest</option>
                    <option value="price_asc" @selected(request('sort')==='price_asc')>Price ↑</option>
                    <option value="price_desc" @selected(request('sort')==='price_desc')>Price ↓</option>
                </select>
            </form>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($products as $product)
                    <x-product-card :product="$product" />
                @empty
                    <p class="text-jamsora-muted col-span-full py-12 text-center">No products found.</p>
                @endforelse
            </div>
            <div class="mt-10">{{ $products->links() }}</div>
        </div>
    </div>
</div>
@endsection
