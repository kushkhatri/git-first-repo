@props(['product', 'showQuick' => true])

<article class="product-card-dagas">
    <a href="{{ route('products.show', $product->slug) }}" class="product-thumb block">
        @if($product->primaryImageUrl())
            <img src="{{ $product->primaryImageUrl() }}" alt="{{ $product->name }}"
                 class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
        @else
            <div class="w-full h-full flex items-center justify-center text-jamsora-subtle text-xs uppercase tracking-widest">No image</div>
        @endif

        @if($product->isOnSale())
            @php $pct = $product->price > 0 ? round((1 - $product->sale_price / $product->price) * 100) : 0; @endphp
            <span class="absolute top-3 left-3 bg-jamsora-gold text-white text-[10px] font-semibold uppercase tracking-wider px-2 py-1">-{{ $pct }}%</span>
        @endif
        @if($product->is_featured)
            <span class="absolute top-3 right-3 bg-jamsora-ink text-white text-[10px] font-semibold uppercase tracking-wider px-2 py-1">Hot</span>
        @endif

        @if($showQuick)
            <div class="product-actions" @click.prevent>
                <form action="{{ route('cart.store') }}" method="post" class="inline">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="action-btn">Add to cart</button>
                </form>
                <a href="{{ route('products.show', $product->slug) }}" class="action-btn">Quick view</a>
            </div>
        @endif
    </a>

    <div class="pt-5 pb-2 text-center">
        @if($product->category)
            <p class="text-[10px] uppercase tracking-[0.2em] text-jamsora-subtle mb-1">{{ $product->category->name }}</p>
        @endif
        <h3 class="font-display text-lg text-jamsora-ink leading-snug px-2">
            <a href="{{ route('products.show', $product->slug) }}" class="hover:text-jamsora-gold transition">{{ $product->name }}</a>
        </h3>
        <div class="mt-2 flex items-center justify-center gap-2 text-sm">
            @if($product->isOnSale())
                <span class="text-jamsora-subtle line-through">${{ number_format($product->price, 2) }}</span>
                <span class="text-jamsora-gold-dark font-medium">${{ number_format($product->sale_price, 2) }}</span>
            @else
                <span class="text-jamsora-ink font-medium">${{ number_format($product->price, 2) }}</span>
            @endif
        </div>
    </div>
</article>
