<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Faq;
use App\Models\Game;
use App\Models\Page;
use App\Models\PaymentGateway;

class PageController extends Controller
{
    public function home()
    {
        $banners = Banner::active()->orderBy('sort_order')->get();

        return view('customer.home', compact('banners'));
    }

    public function gameDetail(string $slug)
    {
        $game = Game::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $midtrans = PaymentGateway::where('code', 'midtrans')->where('is_active', true)->first();

        return view('customer.game-detail', [
            'slug' => $slug,
            'gameName' => $game->name,
            'midtransClientKey' => $midtrans ? config('midtrans.client_key', '') : '',
            'midtransIsProduction' => $midtrans ? (bool) config('midtrans.is_production', false) : false,
        ]);
    }

    public function orderStatus(?string $invoice = null)
    {
        return view('customer.order-status', [
            'invoice' => $invoice,
        ]);
    }

    public function faq()
    {
        $faqs = Faq::active()->orderBy('sort_order')->get();

        return view('customer.faq', compact('faqs'));
    }

    public function terms()
    {
        $page = Page::where('slug', 'terms')->firstOrFail();

        return view('customer.static-page', compact('page'));
    }

    public function privacy()
    {
        $page = Page::where('slug', 'privacy')->firstOrFail();

        return view('customer.static-page', compact('page'));
    }
}