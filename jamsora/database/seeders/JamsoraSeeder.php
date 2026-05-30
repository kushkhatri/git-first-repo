<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\SliderSlide;
use App\Models\Tag;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class JamsoraSeeder extends Seeder
{
    public function run(): void
    {
        $roles = collect([
            ['name' => 'Super Admin', 'slug' => 'super_admin'],
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'Manager', 'slug' => 'manager'],
            ['name' => 'Customer', 'slug' => 'customer'],
        ])->map(fn ($r) => Role::firstOrCreate(['slug' => $r['slug']], $r));

        $adminRole = $roles->firstWhere('slug', 'admin');
        $customerRole = $roles->firstWhere('slug', 'customer');

        User::updateOrCreate(
            ['email' => 'admin@jamsora.com'],
            [
                'name' => 'Jamsora Admin',
                'password' => bcrypt('password'),
                'role_id' => $adminRole->id,
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@jamsora.com'],
            [
                'name' => 'Demo Customer',
                'password' => bcrypt('password'),
                'role_id' => $customerRole->id,
                'status' => 'active',
            ]
        );

        $settings = app(SettingService::class);
        $settings->set('site_name', 'Jamsora', 'store');
        $settings->set('tagline', 'Fine gemstones & jewelry', 'store');
        $settings->set('contact_email', 'hello@jamsora.com', 'store');

        $this->seedCategories();

        $csvPath = database_path('data/products.csv');
        $csvValid = is_readable($csvPath) && ! str_contains((string) file_get_contents($csvPath, false, null, 0, 100), '<!DOCTYPE');

        if ($csvValid) {
            $this->command?->call('products:import', ['file' => $csvPath]);
        } else {
            try {
                $this->command?->call('products:sync-reference', ['--limit' => 24]);
            } catch (\Throwable $e) {
                $this->command?->warn('Reference sync failed, using compact fallback: '.$e->getMessage());
                $this->seedProductsFromReference();
            }
        }

        $this->seedCmsPages();

        $slider = Slider::firstOrCreate(['name' => 'Home Hero'], ['status' => 'active']);
        SliderSlide::updateOrCreate(
            ['slider_id' => $slider->id, 'sort_order' => 0],
            [
                'title' => 'Exceptional gemstones',
                'subtitle' => 'Direct from Jamsora',
                'image' => 'https://shop.jamsora.com/wp-content/uploads/1.jpg',
                'link' => '/shop',
                'status' => 'active',
            ]
        );

        Tag::firstOrCreate(['slug' => 'bestseller'], ['name' => 'Bestseller']);
        Tag::firstOrCreate(['slug' => 'trend'], ['name' => 'Trend']);
    }

    private function seedCategories(): void
    {
        try {
            $response = Http::timeout(30)->get('https://shop.jamsora.com/wp-json/wc/store/v1/products/categories', [
                'per_page' => 20,
            ]);
            if ($response->successful()) {
                foreach ($response->json() as $cat) {
                    Category::updateOrCreate(
                        ['slug' => $cat['slug']],
                        [
                            'name' => $cat['name'],
                            'description' => $cat['description'] ?? null,
                            'status' => 'active',
                            'sort_order' => 0,
                        ]
                    );
                }

                return;
            }
        } catch (\Throwable) {
            // fallback below
        }

        foreach (['Sapphire', 'Emerald', 'Ruby', 'Peridot', 'Aquamarine'] as $name) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'status' => 'active']
            );
        }
    }

    private function seedProductsFromReference(): void
    {
        try {
            $response = Http::timeout(45)->get('https://shop.jamsora.com/wp-json/wc/store/v1/products', [
                'per_page' => 12,
            ]);
            if (! $response->successful()) {
                $this->seedFallbackProducts();

                return;
            }

            foreach ($response->json() as $item) {
                $category = null;
                if (! empty($item['categories'][0]['slug'])) {
                    $category = Category::where('slug', $item['categories'][0]['slug'])->first();
                }

                $regular = ((float) ($item['prices']['regular_price'] ?? 0)) / 100;
                $sale = ((float) ($item['prices']['sale_price'] ?? 0)) / 100;
                $price = $regular > 0 ? $regular : $sale;
                $salePrice = ($item['on_sale'] ?? false) && $sale > 0 ? $sale : null;

                $attrs = [];
                foreach ($item['attributes'] ?? [] as $attr) {
                    $key = Str::slug($attr['name'], '_');
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
                        'stock_qty' => 1,
                        'manage_stock' => true,
                        'status' => 'published',
                        'is_featured' => true,
                        'gem_attributes' => $attrs,
                    ]
                );

                foreach ($item['images'] ?? [] as $i => $img) {
                    ProductImage::updateOrCreate(
                        ['product_id' => $product->id, 'path' => $img['src']],
                        ['is_primary' => $i === 0, 'sort_order' => $i, 'alt_text' => $item['name']]
                    );
                }
            }

            return;
        } catch (\Throwable) {
            $this->seedFallbackProducts();
        }
    }

    private function seedFallbackProducts(): void
    {
        $demos = [
            ['sku' => 'JRO-G627493954', 'name' => '2.52 cts Untreated Oval Sapphire', 'slug' => '2-52-cts-untreated-oval-sapphire', 'cat' => 'Sapphire', 'price' => 588.18, 'sale' => 490.15, 'attrs' => ['shape' => 'Oval', 'carat' => '2.52', 'certificate' => 'IGI']],
            ['sku' => 'JRO-SA203261383', 'name' => '6.43 cts Untreated Oval Sapphire', 'slug' => '6-43-cts-untreated-oval-sapphire', 'cat' => 'Sapphire', 'price' => 1715.22, 'sale' => 1429.35, 'attrs' => ['shape' => 'Oval', 'carat' => '6.43', 'certificate' => 'IGI']],
            ['sku' => 'JRO-G250905129', 'name' => '5.1 cts Untreated Oval Peridot', 'slug' => '5-1-cts-untreated-oval-peridot', 'cat' => 'Peridot', 'price' => 340.11, 'sale' => 283.43, 'attrs' => ['shape' => 'Oval', 'carat' => '5.1', 'certificate' => 'IGI']],
            ['sku' => 'JRO-P658885649', 'name' => '16.85 cts Untreated Oval Peridot', 'slug' => '16-85-cts-untreated-oval-peridot', 'cat' => 'Peridot', 'price' => 2247.39, 'sale' => 1872.83, 'attrs' => ['shape' => 'Oval', 'carat' => '16.85', 'certificate' => 'IGI']],
        ];
        $img = 'https://shop.jamsora.com/wp-content/uploads/1.jpg';
        foreach ($demos as $i => $d) {
            $category = Category::firstOrCreate(['slug' => \Illuminate\Support\Str::slug($d['cat'])], ['name' => $d['cat'], 'status' => 'active']);
            $product = Product::updateOrCreate(
                ['sku' => $d['sku']],
                [
                    'category_id' => $category->id,
                    'name' => $d['name'],
                    'slug' => $d['slug'],
                    'short_description' => 'Certified untreated gemstone from Jamsora collection.',
                    'price' => $d['price'],
                    'sale_price' => $d['sale'],
                    'stock_qty' => 1,
                    'status' => 'published',
                    'is_featured' => $i < 8,
                    'gem_attributes' => $d['attrs'],
                ]
            );
            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'is_primary' => true],
                ['path' => $img, 'alt_text' => $d['name'], 'sort_order' => 0, 'is_primary' => true]
            );
        }
    }

    private function seedCmsPages(): void
    {
        Page::updateOrCreate(
            ['slug' => 'price-match-guarantee'],
            [
                'title' => 'Price Match Guarantee',
                'status' => 'published',
                'body' => '<p>As a direct-to-customer company, Jamsora\'s prices are among the most competitive in the industry. If you find a comparable gemstone for less, we will honor the competing price subject to our terms.</p><h2>How to Request a Price Match</h2><p>Contact our team with details of the competing offer for review.</p>',
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'about-us'],
            [
                'title' => 'About Jamsora',
                'status' => 'published',
                'body' => '<p>Jamsora offers curated fine gemstones with certification and transparent pricing — inspired by our collection at shop.jamsora.com.</p>',
            ]
        );
    }
}
