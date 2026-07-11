<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\PaymentGateway;

class PageController extends Controller
{
    public function home()
    {
        return view('customer.home');
    }

    public function gameDetail(string $slug)
    {
        $game = Game::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $midtrans = PaymentGateway::where('code', 'midtrans')->where('is_active', true)->first();

        return view('customer.game-detail', [
            'slug' => $slug,
            'gameName' => $game->name,
            'midtransClientKey' => $midtrans->api_key ?? '',
            'midtransIsProduction' => $midtrans ? ! $midtrans->is_sandbox : false,
        ]);
    }

    public function orderStatus(?string $invoice = null)
    {
        return view('customer.order-status', [
            'invoice' => $invoice,
        ]);
    }
}