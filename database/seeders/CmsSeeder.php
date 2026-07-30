<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Page;
use Illuminate\Database\Seeder;

class CmsSeeder extends Seeder
{
    public function run(): void
    {
        Page::updateOrCreate(
            ['slug' => 'terms'],
            [
                'title' => 'Syarat & Ketentuan',
                'content' => <<<'HTML'
                    <p>Selamat datang di TopUp Kilat. Dengan menggunakan layanan ini, kamu dianggap menyetujui syarat & ketentuan di bawah.</p>
                    <h3>1. Layanan</h3>
                    <p>TopUp Kilat menyediakan jasa pengisian ulang (top up) item digital game secara otomatis melalui pihak ketiga (provider).</p>
                    <h3>2. Pesanan & Pembayaran</h3>
                    <p>Pastikan ID game dan nominal yang dipilih sudah benar sebelum melakukan pembayaran. Kesalahan input ID akibat kelalaian pengguna bukan tanggung jawab TopUp Kilat.</p>
                    <h3>3. Estimasi Proses</h3>
                    <p>Estimasi waktu proses top up adalah 1-3 menit setelah pembayaran dikonfirmasi, namun dapat lebih lama tergantung kondisi provider.</p>
                    <h3>4. Refund</h3>
                    <p>Pengembalian dana hanya berlaku untuk transaksi yang gagal diproses oleh sistem, sesuai kebijakan yang berlaku.</p>
                    HTML,
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'privacy'],
            [
                'title' => 'Kebijakan Privasi',
                'content' => <<<'HTML'
                    <p>TopUp Kilat menghormati privasi penggunanya. Kebijakan ini menjelaskan data apa saja yang kami kumpulkan dan bagaimana penggunaannya.</p>
                    <h3>1. Data yang Dikumpulkan</h3>
                    <p>Kami mengumpulkan email, nama, dan riwayat transaksi untuk keperluan pemrosesan pesanan dan dukungan pelanggan.</p>
                    <h3>2. Penggunaan Data</h3>
                    <p>Data digunakan semata-mata untuk memproses transaksi, mengirim notifikasi status pesanan, dan meningkatkan kualitas layanan.</p>
                    <h3>3. Keamanan</h3>
                    <p>Data sensitif seperti kredensial pembayaran diproses langsung oleh payment gateway resmi dan tidak disimpan di server kami.</p>
                    HTML,
            ]
        );

        $faqs = [
            ['Berapa lama proses top up?', 'Rata-rata 1-3 menit setelah pembayaran berhasil dikonfirmasi. Beberapa game bisa lebih cepat atau sedikit lebih lama tergantung kondisi provider.'],
            ['Bagaimana cara mengecek ID game saya?', 'Setiap halaman game punya petunjuk letak ID di bagian "Petunjuk Letak ID Game". Biasanya ada di halaman profil dalam game.'],
            ['Metode pembayaran apa saja yang tersedia?', 'QRIS, Virtual Account, dan berbagai e-wallet populer lewat Midtrans.'],
            ['Pesanan saya gagal, apa yang harus dilakukan?', 'Cek status transaksi di halaman Cek Transaksi menggunakan nomor invoice. Kalau statusnya Failed, dana otomatis masuk proses pengembalian.'],
            ['Apakah perlu membuat akun untuk top up?', 'Tidak wajib. Kamu bisa checkout sebagai tamu, tapi dengan login kamu bisa melihat riwayat transaksi dan pakai fitur Pesan Lagi.'],
        ];

        foreach ($faqs as $index => [$question, $answer]) {
            Faq::updateOrCreate(
                ['question' => $question],
                ['answer' => $answer, 'sort_order' => $index, 'is_active' => true]
            );
        }
    }
}
