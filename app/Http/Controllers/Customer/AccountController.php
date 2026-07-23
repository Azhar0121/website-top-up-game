<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

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