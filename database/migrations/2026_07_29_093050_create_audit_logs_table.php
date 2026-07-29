<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Nullable: kalau admin yang bersangkutan sudah dihapus dari sistem,
            // riwayat aktivitasnya tetap harus ada
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // login, created, updated, deleted, force_success, status_override, dst.
            $table->string('action', 50);

            // Morph ke model yang kena aksi (Product, Category, Voucher, Order, dll) -
            // nullable karena aksi 'login' tidak punya subject spesifik.
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();

            $table->string('description', 500);

            // Rincian field apa saja yang berubah
            $table->json('changes')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};