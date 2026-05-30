<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id', 'name', 'slug', 'description', 'image',
        'meta_title', 'meta_description', 'seo_content', 'stone_content', 'og_image',
        'sort_order', 'status',
    ];

    protected function casts(): array
    {
        return [
            'stone_content' => 'array',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function faqs(): MorphMany
    {
        return $this->morphMany(Faq::class, 'faqable')->where('status', 'active')->orderBy('sort_order');
    }

    public function stoneSection(string $key, ?string $default = null): ?string
    {
        $sections = $this->stone_content ?? [];

        return $sections[$key] ?? $default;
    }
}
