<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\SeoSchemaService;
use App\Services\StoneContentService;

class ProductController extends Controller
{
    public function __construct(
        private readonly SeoSchemaService $seo,
        private readonly StoneContentService $stoneContent,
    ) {}

    public function show(string $slug)
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with(['images', 'category', 'tags', 'certification', 'faqs'])
            ->firstOrFail();

        $this->stoneContent->ensureProductFaqs($product);
        $product->load('faqs');

        $related = Product::query()
            ->where('status', 'published')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('images')
            ->take(4)
            ->get();

        $meta = $this->seo->metaForProduct($product);
        $schemas = [
            $this->seo->organization(),
            $this->seo->product($product),
            $this->seo->breadcrumbs([
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Shop', 'url' => route('shop.index')],
                ...($product->category ? [['name' => $product->category->name, 'url' => url('/'.$product->category->slug)]] : []),
                ['name' => $product->name, 'url' => route('products.show', $product->slug)],
            ]),
        ];

        if ($product->faqs->isNotEmpty()) {
            $schemas[] = $this->seo->faqPage($product->faqs);
        }

        $igiFee = (float) config('jamsora.igi.fee', 100);
        $igiExtraDays = (int) config('jamsora.igi.extra_delivery_days', 7);

        return view('storefront.products.show', compact(
            'product', 'related', 'meta', 'schemas', 'igiFee', 'igiExtraDays'
        ));
    }
}
