<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncReferenceProducts extends Command
{
    protected $signature = 'products:sync-reference {--limit=100 : Max products to import} {--page-size=20 : API page size}';

    protected $description = 'Sync products from shop.jamsora.com WooCommerce Store API (reference catalog)';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $pageSize = min(100, max(1, (int) $this->option('page-size')));
        $imported = 0;
        $page = 1;

        $this->info('Syncing from https://shop.jamsora.com …');

        while ($imported < $limit) {
            $response = Http::timeout(120)->retry(2, 3000)->get('https://shop.jamsora.com/wp-json/wc/store/v1/products', [
                'per_page' => $pageSize,
                'page' => $page,
            ]);

            if (! $response->successful()) {
                $this->error('API request failed: '.$response->status());

                return self::FAILURE;
            }

            $items = $response->json();
            if (empty($items)) {
                break;
            }

            foreach ($items as $item) {
                if ($imported >= $limit) {
                    break 2;
                }

                $this->importItem($item, $imported < 12);
                $imported++;
            }

            $this->line("Page {$page}: {$imported} products…");
            $page++;
        }

        $this->info("Synced {$imported} products.");

        return self::SUCCESS;
    }

    private function importItem(array $item, bool $featured = false): void
    {
        $category = null;
        if (! empty($item['categories'][0]['slug'])) {
            $cat = $item['categories'][0];
            $category = Category::updateOrCreate(
                ['slug' => $cat['slug']],
                ['name' => $cat['name'], 'status' => 'active']
            );
        }

        $regular = ((float) ($item['prices']['regular_price'] ?? 0)) / 100;
        $sale = ((float) ($item['prices']['sale_price'] ?? 0)) / 100;
        $price = $regular > 0 ? $regular : $sale;
        $salePrice = ($item['on_sale'] ?? false) && $sale > 0 && $sale < $price ? $sale : null;

        $attrs = [];
        foreach ($item['attributes'] ?? [] as $attr) {
            $key = Str::slug($attr['name'] ?? 'attr', '_');
            $attrs[$key] = $attr['terms'][0]['name'] ?? null;
        }

        $product = Product::updateOrCreate(
            ['sku' => $item['sku'] ?: 'JRO-'.$item['id']],
            [
                'category_id' => $category?->id,
                'name' => $item['name'],
                'slug' => $item['slug'],
                'short_description' => strip_tags($item['short_description'] ?? ''),
                'description' => strip_tags($item['description'] ?? ''),
                'price' => $price ?: 100,
                'sale_price' => $salePrice,
                'stock_qty' => max(0, (int) ($item['low_stock_remaining'] ?? 1)),
                'manage_stock' => true,
                'status' => 'published',
                'is_featured' => $featured,
                'gem_attributes' => array_filter($attrs),
            ]
        );

        foreach ($item['images'] ?? [] as $i => $img) {
            if (empty($img['src'])) {
                continue;
            }
            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'path' => $img['src']],
                ['is_primary' => $i === 0, 'sort_order' => $i, 'alt_text' => $item['name']]
            );
        }
    }
}
