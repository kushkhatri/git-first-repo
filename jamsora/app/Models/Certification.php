<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Certification extends Model
{
    protected $fillable = [
        'name', 'agency', 'certificate_number', 'verification_info',
        'image_path', 'pdf_path', 'stone_details',
    ];

    protected function casts(): array
    {
        return ['stone_details' => 'array'];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
