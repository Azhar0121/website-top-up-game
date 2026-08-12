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
                    <p>Selamat datang di TopUp Kilat. Halaman ini berisi syarat &amp; ketentuan penggunaan layanan kami. Dengan mengakses situs dan melakukan transaksi di TopUp Kilat, kamu dianggap telah membaca, memahami, dan menyetujui seluruh ketentuan di bawah ini. Jika kamu tidak menyetujui salah satu poin, mohon untuk tidak melanjutkan transaksi.</p>

                    <h3>1. Layanan Kami</h3>
                    <p>TopUp Kilat menyediakan jasa pengisian ulang (top up) item digital game seperti diamond, voucher, dan mata uang dalam game lainnya secara otomatis melalui pihak ketiga (provider) yang bekerja sama resmi dengan kami. Kami berusaha memastikan proses berjalan cepat dan aman, namun kecepatan proses tetap bergantung pada kondisi sistem provider dan penerbit game terkait.</p>

                    <h3>2. Pesanan &amp; Pembayaran</h3>
                    <p>Sebelum melakukan pembayaran, pastikan ID game, server (jika ada), dan nominal produk yang kamu pilih sudah benar. Kesalahan input data akibat kelalaian pengguna sepenuhnya menjadi tanggung jawab pengguna dan bukan tanggung jawab TopUp Kilat, sehingga saldo/item yang sudah terkirim ke akun yang salah tidak dapat ditarik kembali. Pembayaran dianggap sah setelah sistem menerima konfirmasi dari payment gateway resmi yang kami gunakan.</p>

                    <h3>3. Estimasi Waktu Proses</h3>
                    <p>Estimasi waktu proses top up adalah 1-3 menit setelah pembayaran berhasil dikonfirmasi. Pada kondisi tertentu seperti gangguan pada sistem provider, jam sibuk, maintenance dari pihak penerbit game, atau kendala jaringan, waktu proses dapat menjadi lebih lama dari estimasi normal. Kami akan menginformasikan status pesanan secara berkala melalui halaman Cek Transaksi.</p>

                    <h3>4. Pembatalan &amp; Refund</h3>
                    <p>Pengembalian dana (refund) hanya berlaku untuk transaksi yang gagal diproses oleh sistem kami atau provider, dan akan diproses sesuai dengan kebijakan pengembalian dana yang berlaku. Transaksi yang sudah berhasil masuk ke akun game tujuan tidak dapat dibatalkan maupun di-refund, kecuali terbukti ada kesalahan sistem di pihak kami.</p>

                    <h3>5. Tanggung Jawab Pengguna</h3>
                    <p>Pengguna wajib menjaga kerahasiaan akun, kata sandi, dan data pribadi lainnya yang digunakan saat bertransaksi di TopUp Kilat. Segala aktivitas yang terjadi melalui akun pengguna menjadi tanggung jawab pemilik akun, kecuali dapat dibuktikan bahwa terjadi pelanggaran keamanan dari sisi sistem kami.</p>

                    <h3>6. Perubahan Ketentuan</h3>
                    <p>TopUp Kilat berhak mengubah, menambah, atau memperbarui syarat &amp; ketentuan ini sewaktu-waktu tanpa pemberitahuan sebelumnya. Perubahan akan berlaku sejak dipublikasikan di halaman ini, sehingga kami menyarankan pengguna untuk memeriksa halaman ini secara berkala.</p>

                    <h3>7. Kontak</h3>
                    <p>Jika kamu memiliki pertanyaan mengenai syarat &amp; ketentuan ini, silakan hubungi tim kami melalui halaman Hubungi Kami.</p>
                    HTML,
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'privacy'],
            [
                'title' => 'Kebijakan Privasi',
                'content' => <<<'HTML'
                    <p>TopUp Kilat menghormati dan menjaga privasi setiap penggunanya. Kebijakan privasi ini menjelaskan data apa saja yang kami kumpulkan, bagaimana data tersebut digunakan, dan hak-hak yang kamu miliki atas data pribadimu saat menggunakan layanan kami.</p>

                    <h3>1. Data yang Kami Kumpulkan</h3>
                    <p>Kami mengumpulkan data seperti nama, alamat email, nomor WhatsApp (jika diberikan), ID game, dan riwayat transaksi. Data ini dikumpulkan pada saat kamu melakukan pemesanan, mendaftar akun, atau menghubungi tim dukungan pelanggan kami.</p>

                    <h3>2. Penggunaan Data</h3>
                    <p>Data yang kami kumpulkan digunakan semata-mata untuk memproses transaksi, mengirim notifikasi status pesanan, memberikan dukungan pelanggan, serta meningkatkan kualitas layanan kami secara keseluruhan. Kami tidak menjual atau menyewakan data pribadi pengguna kepada pihak ketiga untuk kepentingan pemasaran.</p>

                    <h3>3. Keamanan Data</h3>
                    <p>Data sensitif seperti kredensial pembayaran diproses langsung oleh payment gateway resmi yang telah tersertifikasi dan tidak pernah disimpan di server kami. Kami menerapkan langkah-langkah keamanan yang wajar untuk melindungi data pengguna dari akses yang tidak sah, namun tidak ada sistem yang dapat menjamin keamanan seratus persen.</p>

                    <h3>4. Berbagi Data dengan Pihak Ketiga</h3>
                    <p>Data pesanan seperti ID game dan nominal produk akan diteruskan kepada provider resmi yang memproses top up, karena hal ini diperlukan untuk menyelesaikan transaksi. Kami hanya membagikan data seperlunya dan memastikan mitra kami juga menjaga kerahasiaan data pengguna.</p>

                    <h3>5. Hak Pengguna</h3>
                    <p>Pengguna berhak meminta akses, koreksi, atau penghapusan data pribadinya dengan menghubungi tim kami melalui halaman Hubungi Kami. Kami akan menindaklanjuti permintaan tersebut sesuai dengan ketentuan yang berlaku.</p>

                    <h3>6. Perubahan Kebijakan</h3>
                    <p>Kebijakan privasi ini dapat diperbarui dari waktu ke waktu mengikuti perkembangan layanan kami. Setiap perubahan akan dipublikasikan di halaman ini beserta tanggal pembaruannya.</p>
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