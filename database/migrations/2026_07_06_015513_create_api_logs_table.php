<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->nullOnDelete();

            $table->enum('type', ['request', 'response', 'webhook', 'error', 'timeout']);

            $table->longText('headers')->nullable();
            $table->longText('payload')->nullable();   // data yang dikirim
            $table->longText('response')->nullable();  // data yang diterima
            $table->integer('http_status')->nullable();

            $table->timestamps();

            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};