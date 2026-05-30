<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportProductsFromCsv extends Command
{
    protected $signature = 'products:import {file=database/data/products.csv : Path to CSV file} {--fresh : Truncate existing products before import}';

    protected $description = 'Import products from a CSV file (Google Sheets export)';

    public function handle(): int
    {
        $path = $this->argument('file');

        if (! is_readable($path)) {
            $this->error("File not readable: {$path}");
            $this->line('Export your sheet: File → Download → CSV, then save as database/data/products.csv');

            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            Product::query()->forceDelete();
            $this->warn('Existing products removed.');
        }

        $handle = fopen($path, 'r');
        $headers = array_map(fn ($h) => Str::slug(trim($h), '_'), fgetcsv($handle) ?: []);

        if (empty($headers)) {
            $this->error('CSV has no header row.');

            return self::FAILURE;
        }

        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count(array_filter($row)) === 0) {
                continue;
            }

            $data = array_combine($headers, array_pad($row, count($headers), null));
            if ($data === false) {
                continue;
            }

            $this->importRow($data);
            $count++;
        }

        fclose($handle);
        $this->info("Imported {$count} products from {$path}.");

        return self::SUCCESS;
    }

    private function importRow(array $data): void
    {
        $name = $this->val($data, ['name', 'product_name', 'title']);
        if (! $name) {
            return;
        }

        $sku = $this->val($data, ['sku', 'product_sku', 'code']) ?: 'JRO-'.Str::upper(Str::random(8));
        $slug = $this->val($data, ['slug']) ?: Str::slug($name);

        $categoryName = $this->val($data, ['category', 'category_name', 'gems_type', 'gem_type']);
        $category = null;
        if ($categoryName) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName, 'status' => 'active']
            );
        }

        $price = $this->money($this->val($data, ['price', 'regular_price', 'amount']));
        $salePrice = $this->money($this->val($data, ['sale_price', 'sale', 'discount_price']));

        $product = Product::updateOrCreate(
            ['sku' => $sku],
            [
                'category_id' => $category?->id,
                'name' => $name,
                'slug' => $slug,
                'short_description' => $this->val($data, ['short_description', 'short_desc', 'excerpt']),
                'description' => $this->val($data, ['description', 'body', 'details']),
                'price' => $price ?: 0,
                'sale_price' => $salePrice,
                'stock_qty' => (int) ($this->val($data, ['stock_qty', 'stock', 'quantity']) ?: 1),
                'manage_stock' => true,
                'status' => strtolower($this->val($data, ['status']) ?: 'published') === 'draft' ? 'draft' : 'published',
                'is_featured' => in_array(strtolower($this->val($data, ['is_featured', 'featured']) ?? ''), ['1', 'yes', 'true'], true),
                'gem_attributes' => array_filter([
                    'shape' => $this->val($data, ['shape', 'pa_shape']),
                    'carat' => $this->val($data, ['carat', 'pa_carat', 'cts']),
                    'certificate' => $this->val($data, ['certificate', 'pa_certificate', 'cert']),
                    'dimension' => $this->val($data, ['dimension', 'dimensions', 'pa_dimension']),
                    'gems_type' => $this->val($data, ['gems_type', 'gem_type', 'type']),
                ]),
            ]
        );

        $imageUrl = $this->val($data, ['image_url', 'image', 'photo', 'thumbnail', 'main_image']);
        if ($imageUrl) {
            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'is_primary' => true],
                ['path' => $imageUrl, 'alt_text' => $name, 'sort_order' => 0, 'is_primary' => true]
            );
        }
    }

    private function val(array $data, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (! empty($data[$key])) {
                return trim((string) $data[$key]);
            }
        }

        return null;
    }

    private function money(?string $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $clean = preg_replace('/[^0-9.]/', '', $value);

        return $clean !== '' ? (float) $clean : null;
    }
}
