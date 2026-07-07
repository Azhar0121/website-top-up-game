<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTopUpOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(protected Order $order)
    {
    }

    public function handle(OrderService $orderService): void
    {
        $this->order->refresh();

        if ($this->order->status !== Order::STATUS_PAID) {
            return;
        }

        $orderService->dispatchToProvider($this->order);
    }
}