<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderAddress extends Model
{
    protected $fillable = [
        'order_id', 'type', 'name', 'phone', 'address_line1', 'address_line2',
        'city', 'state', 'country', 'zip',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
