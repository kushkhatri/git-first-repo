<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Services\StoneContentService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SeedStoneCategories extends Command
{
    protected $signature = 'stones:seed-categories';

    protected $description = 'Create stone category pages with SEO content and FAQs';

    public function handle(StoneContentService $stoneContent): int
    {
        $stones = ['Diamonds', 'Moissanite', 'Ruby', 'Emerald', 'Sapphire'];

        foreach ($stones as $name) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'status' => 'active']
            );
            $stoneContent->ensureCategoryContent($category);
            $this->line("Seeded: /{$category->slug}");
        }

        $this->info('Stone categories ready.');

        return self::SUCCESS;
    }
}
