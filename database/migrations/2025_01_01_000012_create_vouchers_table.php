<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['fixed', 'percentage']);
            $table->decimal('value', 15, 2);
            $table->decimal('max_discount', 15, 2)->nullable();
            $table->decimal('min_transaction', 15, 2)->default(0);

            $table->integer('usage_limit')->nullable(); // null = unlimited
            $table->integer('used_count')->default(0);

            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};