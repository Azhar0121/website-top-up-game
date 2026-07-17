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

    /**
     * banner_image & logo_image di kolom database CUMA nyimpen path relatif
     * (contoh: "games/logos/abc.png"), bukan URL lengkap. Accessor di bawah ini
     * mengubahnya jadi URL lengkap (misal "http://localhost:8000/storage/games/logos/abc.png")
     * setiap kali diakses lewat $game->banner_image_url atau $game->logo_image_url.
     *
     * Kenapa perlu: Blade di admin bisa panggil asset('storage/'.$game->banner_image)
     * langsung karena dirender di server, tapi JS di halaman customer cuma menerima data
     * JSON mentah dari API - kalau path relatif itu dikirim apa adanya, browser akan salah
     * artikan sebagai path relatif terhadap URL halaman saat ini (bukan folder storage),
     * makanya gambar sempat tidak muncul di customer meski storage:link sudah benar.
     *
     * $appends membuat kedua accessor ini otomatis ikut ke dalam JSON (termasuk endpoint
     * customer di Api\GameController), tanpa perlu ubah controller satu-satu.
     */
    protected $appends = ['banner_image_url', 'logo_image_url'];

    public function getBannerImageUrlAttribute(): ?string
    {
        return $this->banner_image ? asset('storage/'.$this->banner_image) : null;
    }

    public function getLogoImageUrlAttribute(): ?string
    {
        return $this->logo_image ? asset('storage/'.$this->logo_image) : null;
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}