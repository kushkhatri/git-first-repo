@extends('layouts.storefront')
@section('title', 'Shop')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-12">
    <h1 class="font-serif text-4xl mb-8">Shop gemstones</h1>
    <div class="flex flex-col lg:flex-row gap-8">
        <aside class="lg:w-56 flex-shrink-0">
            <p class="text-sm font-medium text-stone-500 mb-3">Categories</p>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('shop.index') }}" class="{{ !request('category') ? 'text-amber-800 font-medium' : 'text-stone-600 hover:text-amber-800' }}">All</a></li>
                @foreach($categories as $cat)
                    <li><a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="{{ request('category') === $cat->slug ? 'text-amber-800 font-medium' : 'text-stone-600 hover:text-amber-800' }}">{{ $cat->name }}</a></li>
                @endforeach
            </ul>
        </aside>
        <div class="flex-1">
            <form class="mb-6 flex gap-2" method="get">
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search…" class="flex-1 rounded-lg border-stone-300">
                <select name="sort" class="rounded-lg border-stone-300 text-sm" onchange="this.form.submit()">
                    <option value="">Sort: Newest</option>
                    <option value="price_asc" @selected(request('sort')==='price_asc')>Price: Low to High</option>
                    <option value="price_desc" @selected(request('sort')==='price_desc')>Price: High to Low</option>
                </select>
            </form>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($products as $product)
                    <x-product-card :product="$product" />
                @empty
                    <p class="text-stone-500 col-span-full">No products found.</p>
                @endforelse
            </div>
            <div class="mt-8">{{ $products->links() }}</div>
        </div>
    </div>
</div>
@endsection
