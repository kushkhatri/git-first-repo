<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Certification;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;

class ProductImportService
{
    public function __construct(
        private readonly StoneContentService $stoneContent,
    ) {}

    /**
     * @return array{imported: int, path: string}
     */
    public function importFromPath(string $path, bool $fresh = false): array
    {
        if (! is_readable($path)) {
            throw new \InvalidArgumentException("File not readable: {$path}");
        }

        $peek = file_get_contents($path, false, null, 0, 200);
        if ($peek && (str_contains($peek, '<!DOCTYPE') || str_contains($peek, '<html'))) {
            throw new \InvalidArgumentException('Invalid CSV: file looks like HTML. Export a real .csv from Google Sheets or WooCommerce.');
        }

        if ($fresh) {
            Product::query()->forceDelete();
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new \RuntimeException('Could not open CSV file.');
        }

        $headers = array_map(fn ($h) => Str::slug(trim((string) $h), '_'), fgetcsv($handle) ?: []);
        if ($headers === []) {
            fclose($handle);
            throw new \InvalidArgumentException('CSV has no header row.');
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

            if ($this->importRow($data)) {
                $count++;
            }
        }

        fclose($handle);

        return ['imported' => $count, 'path' => $path];
    }

    public function importRow(array $data): ?Product
    {
        $data = $this->normalizeRow($data);

        $name = $this->val($data, ['name', 'product_name', 'title']);
        if (! $name) {
            return null;
        }

        $sku = $this->val($data, ['sku', 'product_sku', 'code']) ?: 'JRO-'.Str::upper(Str::random(8));
        $slug = $this->val($data, ['slug', 'url_slug']) ?: Str::slug($name);
        if (Product::where('slug', $slug)->where('sku', '!=', $sku)->exists()) {
            $slug = Str::slug($name).'-'.Str::lower(Str::random(4));
        }

        $category = $this->resolveCategory($data);
        $certification = $this->resolveCertification($data, $sku);

        $price = $this->money($this->val($data, ['price', 'regular_price', 'amount'])) ?: 0;
        $salePrice = $this->money($this->val($data, ['sale_price', 'sale', 'discount_price']));

        $delivery = $this->parseDelivery($this->val($data, ['delivery_time', 'delivery', 'lead_time', 'shipping_class']));

        $product = Product::updateOrCreate(
            ['sku' => $sku],
            [
                'category_id' => $category?->id,
                'certification_id' => $certification?->id,
                'name' => $name,
                'slug' => $slug,
                'stone_type' => $this->val($data, ['stone_type', 'gems_type', 'gem_type', 'stone']),
                'metal_type' => $this->val($data, ['metal_type', 'metal', 'pa_metal']),
                'weight' => $this->val($data, ['weight', 'carat', 'pa_carat', 'cts']),
                'shape' => $this->val($data, ['shape', 'pa_shape']),
                'dimensions' => $this->val($data, ['dimensions', 'dimension', 'pa_dimension']),
                'short_description' => $this->val($data, ['short_description', 'short_desc', 'excerpt']),
                'description' => $this->val($data, ['description', 'body', 'details']),
                'specifications' => $this->parseSpecifications($data),
                'price' => $price,
                'sale_price' => $salePrice,
                'stock_qty' => (int) ($this->val($data, ['stock_qty', 'stock', 'quantity']) ?: 1),
                'stock_status' => $this->stockStatus($data),
                'manage_stock' => true,
                'delivery_days_min' => $delivery['min'],
                'delivery_days_max' => $delivery['max'],
                'igi_available' => ! in_array(strtolower($this->val($data, ['igi_available']) ?? 'yes'), ['no', '0', 'false'], true),
                'status' => $this->resolvePublishStatus($data),
                'is_featured' => in_array(strtolower($this->val($data, ['is_featured', 'featured']) ?? ''), ['1', 'yes', 'true'], true),
                'meta_title' => $this->val($data, ['meta_title', 'seo_title']),
                'meta_description' => $this->val($data, ['meta_description', 'seo_description']),
                'meta_keywords' => $this->val($data, ['meta_keywords', 'keywords']),
                'seo_content' => $this->val($data, ['seo_content']),
                'gem_attributes' => array_filter([
                    'shape' => $this->val($data, ['shape', 'pa_shape']),
                    'carat' => $this->val($data, ['carat', 'pa_carat', 'cts', 'weight']),
                    'certificate' => $this->val($data, ['certificate', 'pa_certificate', 'cert', 'certification_name']),
                    'dimension' => $this->val($data, ['dimension', 'dimensions']),
                    'gems_type' => $this->val($data, ['gems_type', 'gem_type', 'stone_type']),
                ]),
            ]
        );

        $this->importImages($product, $data, $name);

        if ($category) {
            $this->stoneContent->ensureCategoryContent($category);
        }

        return $product;
    }

    /**
     * Map WooCommerce export columns into the canonical import field names.
     */
    private function normalizeRow(array $data): array
    {
        if (! $this->val($data, ['regular_price', 'categories', 'images'])) {
            return $data;
        }

        for ($i = 1; $i <= 5; $i++) {
            $attrName = strtolower(trim((string) ($data["attribute_{$i}_name"] ?? '')));
            $attrVal = trim(preg_replace('/^\h+/u', '', (string) ($data["attribute_{$i}_values"] ?? '')));
            if ($attrName === '' || $attrVal === '') {
                continue;
            }

            if (str_contains($attrName, 'gems') || $attrName === 'gems type') {
                $data['gems_type'] = $attrVal;
            } elseif ($attrName === 'shape') {
                $data['shape'] = $attrVal;
            } elseif (str_contains($attrName, 'dimension')) {
                $data['dimensions'] = $attrVal;
            } elseif (str_contains($attrName, 'certificate') || str_contains($attrName, 'cert')) {
                $data['certificate'] = $attrVal;
            } elseif (str_contains($attrName, 'carat')) {
                $data['carat'] = $attrVal;
            }
        }

        if (empty($data['category']) && ! empty($data['categories'])) {
            $data['category'] = $this->parseCategoryName((string) $data['categories']);
        }

        if (empty($data['delivery_time']) && ! empty($data['shipping_class'])) {
            $data['delivery_time'] = (string) $data['shipping_class'];
        }

        if (! empty($data['images'])) {
            $parts = array_values(array_filter(array_map('trim', preg_split('/\s*,\s*/', (string) $data['images']))));
            if ($parts !== []) {
                $data['image_url'] = $parts[0];
                $data['gallery_images'] = implode('|', $parts);
            }
        }

        return $data;
    }

    private function parseCategoryName(string $raw): string
    {
        $parts = array_map('trim', preg_split('/\s*>\s*/', $raw));

        return $parts[0] ?: $raw;
    }

    private function resolveCategory(array $data): ?Category
    {
        $categoryName = $this->val($data, [
            'categories', 'category', 'category_name', 'stone_category',
        ]) ?? $this->val($data, ['gems_type', 'gem_type', 'stone_type']);

        $subName = $this->val($data, ['subcategory', 'sub_category']);

        if (! $categoryName) {
            return null;
        }

        if (str_contains($categoryName, '>')) {
            $parts = array_map('trim', explode('>', $categoryName));
            $categoryName = $parts[0];
            $subName = $subName ?: ($parts[1] ?? null);
        }

        $parent = Category::firstOrCreate(
            ['slug' => Str::slug($categoryName)],
            ['name' => $categoryName, 'status' => 'active']
        );

        $this->stoneContent->ensureCategoryContent($parent);

        if ($subName) {
            $child = Category::firstOrCreate(
                ['slug' => Str::slug($parent->slug.'-'.$subName)],
                [
                    'parent_id' => $parent->id,
                    'name' => $subName,
                    'status' => 'active',
                ]
            );
            $this->stoneContent->ensureCategoryContent($child);

            return $child;
        }

        return $parent;
    }

    private function resolveCertification(array $data, string $sku): ?Certification
    {
        $certName = $this->val($data, ['certification_name', 'certificate', 'cert', 'pa_certificate']);
        $certNumber = $this->val($data, ['certificate_number', 'cert_number', 'cert_no']);

        if (! $certName && ! $certNumber) {
            return null;
        }

        return Certification::updateOrCreate(
            ['certificate_number' => $certNumber ?: $sku],
            [
                'name' => $certName ?: 'Gemstone Certificate',
                'agency' => $this->val($data, ['certification_agency', 'cert_agency', 'lab']) ?: 'IGI',
                'verification_info' => $this->val($data, ['verification_info', 'cert_verification']),
                'image_path' => $this->val($data, ['certificate_image', 'cert_image_url']),
                'pdf_path' => $this->val($data, ['certificate_pdf', 'cert_pdf_url']),
                'stone_details' => array_filter([
                    'stone_type' => $this->val($data, ['stone_type', 'gems_type']),
                    'weight' => $this->val($data, ['weight', 'carat']),
                    'shape' => $this->val($data, ['shape']),
                    'dimensions' => $this->val($data, ['dimensions', 'dimension']),
                ]),
            ]
        );
    }

    private function importImages(Product $product, array $data, string $name): void
    {
        $urls = [];
        $single = $this->val($data, ['image_url', 'image', 'photo', 'thumbnail', 'main_image']);
        if ($single) {
            $urls[] = $single;
        }
        $gallery = $this->val($data, ['gallery_images', 'images', 'image_urls']);
        if ($gallery) {
            $urls = array_merge($urls, array_map('trim', preg_split('/[|,]/', $gallery)));
        }

        foreach (array_values(array_unique(array_filter($urls))) as $i => $url) {
            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'path' => $url],
                [
                    'alt_text' => $name,
                    'sort_order' => $i,
                    'is_primary' => $i === 0,
                ]
            );
        }
    }

    private function parseSpecifications(array $data): array
    {
        $raw = $this->val($data, ['specifications', 'specs']);
        if ($raw && str_contains($raw, ':')) {
            $specs = [];
            foreach (explode('|', $raw) as $pair) {
                [$k, $v] = array_pad(explode(':', $pair, 2), 2, null);
                if ($k && $v) {
                    $specs[trim($k)] = trim($v);
                }
            }

            return $specs;
        }

        return array_filter([
            'Shape' => $this->val($data, ['shape']),
            'Carat' => $this->val($data, ['carat', 'weight']),
            'Certificate' => $this->val($data, ['certificate']),
            'Dimensions' => $this->val($data, ['dimensions']),
            'Gems Type' => $this->val($data, ['gems_type']),
        ]);
    }

    private function parseDelivery(?string $value): array
    {
        if ($value && preg_match('/(\d+)\s*[-–]\s*(\d+)/', $value, $m)) {
            return ['min' => (int) $m[1], 'max' => (int) $m[2]];
        }

        if ($value && preg_match('/(\d+)\s*[-–]?\s*day/i', $value, $m)) {
            $days = (int) $m[1];

            return ['min' => $days, 'max' => $days];
        }

        return ['min' => 10, 'max' => 14];
    }

    private function resolvePublishStatus(array $data): string
    {
        $published = strtolower($this->val($data, ['published', 'status']) ?? '1');

        if (in_array($published, ['0', 'no', 'false', 'draft'], true)) {
            return 'draft';
        }

        return strtolower($published) === 'draft' ? 'draft' : 'published';
    }

    private function stockStatus(array $data): string
    {
        $inStock = strtolower($this->val($data, ['in_stock', 'stock_status', 'availability']) ?? '1');
        if (in_array($inStock, ['0', 'no', 'false'], true)) {
            return 'out_of_stock';
        }

        $status = strtolower($this->val($data, ['stock_status']) ?? '');

        return in_array($status, ['out_of_stock', 'out of stock', 'sold'], true) ? 'out_of_stock' : 'in_stock';
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
