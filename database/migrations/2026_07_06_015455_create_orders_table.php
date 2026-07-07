<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); 
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('provider_id')->nullable()->constrained('providers'); 

            $table->string('target_game_id');
            $table->string('target_server_id')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_whatsapp')->nullable();

            $table->integer('quantity')->default(1);
            $table->decimal('price', 15, 2);      
            $table->decimal('cost_price', 15, 2)->nullable(); 
            $table->string('voucher_code')->nullable();
            $table->decimal('discount_amount', 15, 2)->default(0);

            $table->enum('status', [
                'pending_payment',
                'paid',
                'processing',
                'success',
                'failed',
                'expired',
                'refunded',
                'cancelled',
            ])->default('pending_payment');

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expired_at')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('invoice_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};