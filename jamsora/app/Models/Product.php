<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'certification_id', 'name', 'slug', 'sku',
        'stone_type', 'metal_type', 'weight', 'shape', 'dimensions',
        'short_description', 'description', 'specifications',
        'price', 'sale_price', 'stock_qty', 'manage_stock', 'status',
        'is_featured', 'delivery_days_min', 'delivery_days_max', 'stock_status',
        'igi_available', 'meta_title', 'meta_description', 'meta_keywords',
        'seo_content', 'og_image', 'gem_attributes',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'manage_stock' => 'boolean',
            'is_featured' => 'boolean',
            'igi_available' => 'boolean',
            'gem_attributes' => 'array',
            'specifications' => 'array',
            'delivery_days_min' => 'integer',
            'delivery_days_max' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function certification(): BelongsTo
    {
        return $this->belongsTo(Certification::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function faqs(): MorphMany
    {
        return $this->morphMany(Faq::class, 'faqable')->where('status', 'active')->orderBy('sort_order');
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
        if ($this->stock_status === 'out_of_stock') {
            return false;
        }

        if (! $this->manage_stock) {
            return true;
        }

        return $this->stock_qty > 0;
    }

    public function deliveryRange(bool $withIgi = false): string
    {
        $min = (int) $this->delivery_days_min;
        $max = (int) $this->delivery_days_max;

        if ($withIgi) {
            $extra = (int) config('jamsora.igi.extra_delivery_days', 7);
            $min += $extra;
            $max += $extra;
        }

        return "{$min}-{$max} Days";
    }

    public function displaySpecifications(): array
    {
        $specs = $this->specifications ?? [];
        $fromColumns = array_filter([
            'Stone Type' => $this->stone_type,
            'Metal Type' => $this->metal_type,
            'Weight' => $this->weight,
            'Shape' => $this->shape,
            'Dimensions' => $this->dimensions,
        ]);

        return array_merge($fromColumns, is_array($specs) ? $specs : []);
    }
}
