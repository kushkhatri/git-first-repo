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

    public function importRow(array $data): ?Product
    {
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
        $certification = $this->resolveCertification($data);

        $price = $this->money($this->val($data, ['price', 'regular_price', 'amount'])) ?: 0;
        $salePrice = $this->money($this->val($data, ['sale_price', 'sale', 'discount_price']));

        $delivery = $this->parseDelivery($this->val($data, ['delivery_time', 'delivery', 'lead_time']));

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
                'status' => strtolower($this->val($data, ['status']) ?: 'published') === 'draft' ? 'draft' : 'published',
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

    private function resolveCategory(array $data): ?Category
    {
        $categoryName = $this->val($data, ['category', 'category_name', 'gems_type', 'gem_type', 'stone_category']);
        $subName = $this->val($data, ['subcategory', 'sub_category']);

        if (! $categoryName) {
            return null;
        }

        $parent = Category::firstOrCreate(
            ['slug' => Str::slug($categoryName)],
            ['name' => $categoryName, 'status' => 'active']
        );

        $this->stoneContent->ensureCategoryContent($parent);

        if ($subName) {
            return Category::firstOrCreate(
                ['slug' => Str::slug($parent->slug.'-'.$subName)],
                [
                    'parent_id' => $parent->id,
                    'name' => $subName,
                    'status' => 'active',
                ]
            );
        }

        return $parent;
    }

    private function resolveCertification(array $data): ?Certification
    {
        $certName = $this->val($data, ['certification_name', 'certificate', 'cert', 'pa_certificate']);
        $certNumber = $this->val($data, ['certificate_number', 'cert_number', 'cert_no']);

        if (! $certName && ! $certNumber) {
            return null;
        }

        return Certification::updateOrCreate(
            ['certificate_number' => $certNumber ?: $certName],
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
            $urls = array_merge($urls, array_map('trim', explode('|', $gallery)));
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
            'Clarity' => $this->val($data, ['clarity']),
            'Color' => $this->val($data, ['color']),
            'Cut' => $this->val($data, ['cut']),
            'Origin' => $this->val($data, ['origin']),
        ]);
    }

    private function parseDelivery(?string $value): array
    {
        if ($value && preg_match('/(\d+)\s*[-–]\s*(\d+)/', $value, $m)) {
            return ['min' => (int) $m[1], 'max' => (int) $m[2]];
        }

        return ['min' => 10, 'max' => 14];
    }

    private function stockStatus(array $data): string
    {
        $status = strtolower($this->val($data, ['stock_status', 'availability']) ?? '');

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
