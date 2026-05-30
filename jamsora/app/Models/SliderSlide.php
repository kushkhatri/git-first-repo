<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SliderSlide extends Model
{
    protected $fillable = [
        'slider_id', 'title', 'subtitle', 'image', 'link', 'sort_order', 'status',
    ];

    public function slider(): BelongsTo
    {
        return $this->belongsTo(Slider::class);
    }
}
