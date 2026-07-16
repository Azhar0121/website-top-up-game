<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Sesuai PRD 4.3: Margin dinamis (Fixed atau Persentase).
            // 'fixed'      -> margin_value adalah nominal rupiah tetap di atas cost_price
            // 'percentage' -> margin_value adalah persentase di atas cost_price
            $table->enum('margin_type', ['fixed', 'percentage'])->default('percentage')->after('base_price');
            $table->decimal('margin_value', 10, 2)->default(10)->after('margin_type'); // default 10%

            // Menandai apakah base_price dihitung otomatis dari cost_price + margin,
            // atau di-set manual oleh admin (override). Kalau true, setiap kali sinkronisasi
            // harga jalan, base_price akan DITIMPA hasil hitungan - kalau false, dibiarkan.
            $table->boolean('auto_price')->default(true)->after('margin_value');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['margin_type', 'margin_value', 'auto_price']);
        });
    }
};