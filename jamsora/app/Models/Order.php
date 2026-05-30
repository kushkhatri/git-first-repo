<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'order_number', 'subtotal', 'discount', 'igi_total', 'shipping_cost',
        'tax', 'total', 'status', 'payment_status', 'payment_method', 'notes',
        'guest_email', 'delivery_days_min', 'delivery_days_max',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'igi_total' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'delivery_days_min' => 'integer',
            'delivery_days_max' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function shippingAddress(): ?OrderAddress
    {
        return $this->addresses->firstWhere('type', 'shipping');
    }

    public function deliveryEstimate(): ?string
    {
        if (! $this->delivery_days_min || ! $this->delivery_days_max) {
            return null;
        }

        return "{$this->delivery_days_min}-{$this->delivery_days_max} Days";
    }
}
