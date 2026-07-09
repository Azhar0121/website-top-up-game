<?php

namespace App\Services;

use App\Models\Voucher;

class VoucherService
{
    public function validate(string $code, float $transactionAmount): array
    {
        $voucher = Voucher::where('code', $code)->where('is_active', true)->first();

        if (! $voucher) {
            return $this->invalid('Kode voucher tidak ditemukan atau sudah tidak aktif');
        }

        if ($voucher->start_date && now()->lt($voucher->start_date)) {
            return $this->invalid('Voucher belum berlaku');
        }

        if ($voucher->end_date && now()->gt($voucher->end_date)) {
            return $this->invalid('Voucher sudah kedaluwarsa');
        }

        if ($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit) {
            return $this->invalid('Kuota voucher sudah habis');
        }

        if ($transactionAmount < $voucher->min_transaction) {
            return $this->invalid(
                "Minimal transaksi untuk voucher ini adalah Rp " . number_format($voucher->min_transaction, 0, ',', '.')
            );
        }

        $discount = $voucher->type === 'fixed'
            ? (float) $voucher->value
            : $transactionAmount * ((float) $voucher->value / 100);

        if ($voucher->type === 'percentage' && $voucher->max_discount) {
            $discount = min($discount, (float) $voucher->max_discount);
        }

        $discount = min($discount, $transactionAmount);

        return [
            'valid'           => true,
            'message'         => 'Voucher berhasil diterapkan',
            'discount_amount' => round($discount, 2),
            'voucher'         => $voucher,
        ];
    }

    public function markAsUsed(Voucher $voucher): void
    {
        $voucher->increment('used_count');
    }

    protected function invalid(string $message): array
    {
        return ['valid' => false, 'message' => $message, 'discount_amount' => 0, 'voucher' => null];
    }
}