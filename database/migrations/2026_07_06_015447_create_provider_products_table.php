<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('provider_sku_code'); 
            $table->decimal('cost_price', 15, 2); 
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['provider_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_products');
    }
};