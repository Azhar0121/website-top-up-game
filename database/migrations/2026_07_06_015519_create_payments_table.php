<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('payment_gateway_id')->constrained('payment_gateways');

            $table->string('method'); 
            $table->string('reference_number')->nullable(); 
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->default(0); 

            $table->enum('status', ['pending', 'paid', 'failed', 'expired'])->default('pending');

            $table->longText('raw_callback')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('reference_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};