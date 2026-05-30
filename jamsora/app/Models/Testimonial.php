<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'name', 'position', 'company', 'avatar', 'rating', 'content', 'status', 'sort_order',
    ];
}
