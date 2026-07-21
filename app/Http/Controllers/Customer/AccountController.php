<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

/**
 * PRD 3 "User Account": riwayat transaksi. Jadi pintu masuk fitur Repeat Order -
 * dari sini customer bisa lihat order lamanya dan klik "Pesan Lagi" tanpa perlu
 * ingat-ingat nomor invoice atau isi form dari nol.
 */
class AccountController extends Controller
{
    public function index()
    {
        $orders = Order::with(['product.game'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('customer.account', compact('orders'));
    }
}
