<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentGateways\PaymentGatewayServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    private const STATUSES = [
        Order::STATUS_PENDING_PAYMENT,
        Order::STATUS_PAID,
        Order::STATUS_PROCESSING,
        Order::STATUS_SUCCESS,
        Order::STATUS_FAILED,
        Order::STATUS_EXPIRED,
        Order::STATUS_REFUNDED,
        Order::STATUS_CANCELLED,
    ];

    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(self::STATUSES)],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $orders = Order::query()
            ->with(['product.game', 'provider', 'payment.paymentGateway'])
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($validated['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('invoice_number', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%")
                        ->orWhere('customer_whatsapp', 'like', "%{$search}%")
                        ->orWhere('target_game_id', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => self::STATUSES,
        ]);
    }

    public function show(Order $order)
    {
        $order->load([
            'product.game',
            'product.category',
            'provider',
            'payment.paymentGateway',
            'logs' => fn ($query) => $query->latest(),
            'apiLogs' => fn ($query) => $query->latest()->limit(10),
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function retry(Order $order, Request $request, OrderService $orderService)
    {
        try {
            $orderService->manualRetry($order, $this->actorName($request));
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('status', 'Retry provider telah dijalankan. Periksa riwayat order untuk hasilnya.');
    }

    public function forceSuccess(Order $order, Request $request, OrderService $orderService)
    {
        $validated = $request->validate([
            'note' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        try {
            $orderService->forceSuccess($order, $this->actorName($request), $validated['note']);
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('status', 'Order ditandai Success secara manual dan tercatat pada riwayat.');
    }

    public function resendCallback(Order $order, Request $request, OrderService $orderService)
    {
        if ($order->status !== Order::STATUS_SUCCESS) {
            return back()->with('error', 'Notifikasi hanya dapat dikirim ulang untuk order berstatus Success.');
        }

        $orderService->resendCallback($order, $this->actorName($request));

        return redirect()->route('admin.orders.show', $order)
            ->with('status', 'Notifikasi sukses dikirim ulang ke pelanggan.');
    }

    /**
     * POST /admin/orders/{order}/check-payment-status
     */
    public function checkPaymentStatus(Order $order, OrderService $orderService)
    {
        $payment = $order->payment()->latest()->first();

        if (! $payment) {
            return back()->with('error', 'Order ini belum punya record pembayaran sama sekali.');
        }

        $service = PaymentGatewayServiceFactory::make($payment->paymentGateway);
        $payload = $service->checkStatus($order);

        if ($payload === null) {
            return back()->with('error', 'Gagal menghubungi payment gateway, atau gateway ini belum mendukung cek status manual. Cek API & Webhook Logs untuk detail errornya.');
        }

        $mappedStatus = $service->mapStatus($payload);
        $alreadyPaid = $payment->status === 'paid';

        $payment->update([
            'status'       => $mappedStatus,
            'raw_callback' => json_encode($payload),
            'paid_at'      => $mappedStatus === 'paid' ? ($payment->paid_at ?? now()) : $payment->paid_at,
        ]);

        if ($mappedStatus === 'paid' && ! $alreadyPaid && $order->status === Order::STATUS_PENDING_PAYMENT) {
            if ($orderService->processAfterPayment($order)) {
                \App\Jobs\ProcessTopUpOrder::dispatch($order->fresh());
            }

            return redirect()->route('admin.orders.show', $order)
                ->with('status', 'Status ditemukan: sudah dibayar. Order dilanjutkan ke provider.');
        }

        if (in_array($mappedStatus, ['failed', 'expired']) && $order->status === Order::STATUS_PENDING_PAYMENT) {
            $order->transitionTo(
                $mappedStatus === 'expired' ? Order::STATUS_EXPIRED : Order::STATUS_CANCELLED,
                "Pembayaran {$mappedStatus} menurut pengecekan manual ke {$payment->paymentGateway->name}"
            );

            return redirect()->route('admin.orders.show', $order)
                ->with('status', "Status ditemukan: {$mappedStatus}. Order diupdate.");
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('status', "Gateway melaporkan status: {$mappedStatus}. Tidak ada perubahan pada order (belum ada progres baru).");
    }

    private function actorName(Request $request): string
    {
        return $request->user()?->name ?: 'admin';
    }
}