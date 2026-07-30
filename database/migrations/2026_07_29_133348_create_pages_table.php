<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            // Slug SENGAJA dibatasi ke 2 nilai tetap ('terms', 'privacy') lewat validasi
            // di controller, bukan bikin CMS halaman bebas - sesuai keputusan skip Blog/
            // halaman custom lain, cukup 2 halaman legal yang memang wajib ada.
            $table->string('slug')->unique();
            $table->string('title');
            $table->longText('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};