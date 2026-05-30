<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'product_id', 'quantity', 'unit_price', 'options'];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'options' => 'array',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function hasIgi(): bool
    {
        return (bool) ($this->options['igi_certification'] ?? false);
    }

    public function igiFee(): float
    {
        if (! $this->hasIgi()) {
            return 0;
        }

        return (float) config('jamsora.igi.fee', 100);
    }

    public function lineTotal(): float
    {
        return ((float) $this->unit_price + $this->igiFee()) * $this->quantity;
    }
}
