<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class SeoSchemaService
{
    public function organization(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => config('app.name', 'Jamsora'),
            'url' => url('/'),
            'logo' => asset('brand/logo-dark.png'),
        ];
    }

    public function breadcrumbs(array $items): array
    {
        $list = [];
        foreach ($items as $i => $item) {
            $list[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name'],
                'item' => $item['url'] ?? null,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list,
        ];
    }

    public function product(Product $product): array
    {
        $images = $product->images->pluck('path')->filter()->values()->all();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'sku' => $product->sku,
            'description' => strip_tags($product->short_description ?: $product->description ?: $product->name),
            'image' => $images ?: [$product->primaryImageUrl()],
            'offers' => [
                '@type' => 'Offer',
                'url' => route('products.show', $product->slug),
                'priceCurrency' => 'USD',
                'price' => $product->effective_price,
                'availability' => $product->inStock()
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
            ],
        ];
    }

    public function faqPage(iterable $faqs): array
    {
        $entities = [];
        foreach ($faqs as $faq) {
            $entities[] = [
                '@type' => 'Question',
                'name' => $faq->question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => strip_tags($faq->answer),
                ],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $entities,
        ];
    }

    public function metaForProduct(Product $product): array
    {
        return [
            'title' => $product->meta_title ?: "{$product->name} | Jamsora",
            'description' => $product->meta_description ?: Str::limit(strip_tags($product->short_description ?: $product->description ?: ''), 160),
            'og_image' => $product->og_image ?: $product->primaryImageUrl(),
            'canonical' => route('products.show', $product->slug),
        ];
    }

    public function metaForCategory(Category $category): array
    {
        return [
            'title' => $category->meta_title ?: "Shop {$category->name} | Jamsora",
            'description' => $category->meta_description ?: Str::limit(strip_tags($category->description ?: ''), 160),
            'og_image' => $category->og_image ?: $category->image,
            'canonical' => URL::to('/'.$category->slug),
        ];
    }
}
