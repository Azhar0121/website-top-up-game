<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('flash_sale_id')->nullable()->after('voucher_code')
                ->constrained()->nullOnDelete();
            $table->decimal('flash_sale_discount', 12, 2)->default(0)->after('flash_sale_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('flash_sale_id');
            $table->dropColumn('flash_sale_discount');
        });
    }
};