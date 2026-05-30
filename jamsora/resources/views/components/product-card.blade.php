@props(['product'])
<a href="{{ route('products.show', $product->slug) }}" class="group block bg-white rounded-lg overflow-hidden border border-stone-200 hover:shadow-lg transition">
    <div class="aspect-square bg-stone-100 overflow-hidden">
        @if($product->primaryImageUrl())
            <img src="{{ $product->primaryImageUrl() }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
        @else
            <div class="w-full h-full flex items-center justify-center text-stone-400 text-sm">No image</div>
        @endif
    </div>
    <div class="p-4">
        @if($product->category)
            <p class="text-xs uppercase tracking-wider text-amber-800 mb-1">{{ $product->category->name }}</p>
        @endif
        <h3 class="font-serif text-lg text-stone-900 line-clamp-2">{{ $product->name }}</h3>
        <div class="mt-2 flex items-baseline gap-2">
            @if($product->isOnSale())
                <span class="text-stone-400 line-through text-sm">${{ number_format($product->price, 2) }}</span>
                <span class="text-amber-900 font-semibold">${{ number_format($product->sale_price, 2) }}</span>
            @else
                <span class="text-amber-900 font-semibold">${{ number_format($product->price, 2) }}</span>
            @endif
        </div>
    </div>
</a>
