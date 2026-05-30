<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'sku', 'short_description', 'description',
        'price', 'sale_price', 'stock_qty', 'manage_stock', 'status',
        'is_featured', 'meta_title', 'meta_description', 'gem_attributes',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'manage_stock' => 'boolean',
            'is_featured' => 'boolean',
            'gem_attributes' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->sale_price ?? $this->price);
    }

    public function isOnSale(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    public function primaryImageUrl(): ?string
    {
        $image = $this->images->firstWhere('is_primary', true) ?? $this->images->first();

        return $image?->path;
    }

    public function inStock(): bool
    {
        if (! $this->manage_stock) {
            return true;
        }

        return $this->stock_qty > 0;
    }
}
