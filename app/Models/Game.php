<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'banner_image',
        'logo_image',
        'tutorial_text',
        'description',
        'is_favorite',
        'is_popular',
        'is_active',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
        'is_popular'  => 'boolean',
        'is_active'   => 'boolean',
    ];

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}