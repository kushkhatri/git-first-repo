@props(['product'])
<a href="{{ route('products.show', $product->slug) }}" class="group block bg-white overflow-hidden border border-jamsora-border hover:shadow-card transition duration-300">
    <div class="aspect-square bg-jamsora-cream overflow-hidden relative">
        @if($product->primaryImageUrl())
            <img src="{{ $product->primaryImageUrl() }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-[1.03] transition duration-700 ease-out">
        @else
            <div class="w-full h-full flex items-center justify-center text-jamsora-subtle text-xs uppercase tracking-widest">No image</div>
        @endif
        @if($product->isOnSale())
            <span class="absolute top-3 left-3 bg-jamsora-ink text-white text-[10px] uppercase tracking-widest px-2 py-1">Sale</span>
        @endif
    </div>
    <div class="p-5">
        @if($product->category)
            <p class="text-[10px] uppercase tracking-[0.25em] text-jamsora-champagne mb-2">{{ $product->category->name }}</p>
        @endif
        <h3 class="font-display text-lg text-jamsora-ink leading-snug line-clamp-2 group-hover:text-jamsora-champagne-dark transition">{{ $product->name }}</h3>
        <div class="mt-3 flex items-baseline gap-2">
            @if($product->isOnSale())
                <span class="text-jamsora-subtle line-through text-sm">${{ number_format($product->price, 2) }}</span>
                <span class="text-jamsora-ink font-medium">${{ number_format($product->sale_price, 2) }}</span>
            @else
                <span class="text-jamsora-ink font-medium">${{ number_format($product->price, 2) }}</span>
            @endif
        </div>
    </div>
</a>
