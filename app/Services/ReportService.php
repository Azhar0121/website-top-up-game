<?php

namespace App\Services;

use App\Models\ApiLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\Provider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public const REVENUE_STATUSES = [
        Order::STATUS_PAID,
        Order::STATUS_PROCESSING,
        Order::STATUS_SUCCESS,
        Order::STATUS_FAILED,
    ];

    public const FINAL_STATUSES = [
        Order::STATUS_SUCCESS,
        Order::STATUS_FAILED,
        Order::STATUS_EXPIRED,
        Order::STATUS_REFUNDED,
        Order::STATUS_CANCELLED,
    ];

    public function todayKpi(): array
    {
        $today = Carbon::today();

        $salesToday = Order::whereDate('created_at', $today)
            ->whereIn('status', self::REVENUE_STATUSES)
            ->sum('price');

        $profitToday = Order::whereDate('created_at', $today)
            ->where('status', Order::STATUS_SUCCESS)
            ->selectRaw('COALESCE(SUM(price - COALESCE(cost_price, 0)), 0) as profit')
            ->value('profit');

        $pendingToday = Order::whereDate('created_at', $today)
            ->whereIn('status', [Order::STATUS_PENDING_PAYMENT, Order::STATUS_PAID, Order::STATUS_PROCESSING])
            ->count();

        $successToday = Order::whereDate('created_at', $today)->where('status', Order::STATUS_SUCCESS)->count();
        $finalToday = Order::whereDate('created_at', $today)->whereIn('status', self::FINAL_STATUSES)->count();
        $successRatio = $finalToday > 0 ? round(($successToday / $finalToday) * 100, 1) : null;

        $bestSeller = Order::whereDate('created_at', $today)
            ->whereIn('status', self::REVENUE_STATUSES)
            ->select('product_id', DB::raw('SUM(quantity) as qty_sold'))
            ->groupBy('product_id')
            ->orderByDesc('qty_sold')
            ->with('product:id,name')
            ->first();

        $last7Days = $this->salesRevenue(Carbon::today()->subDays(6), Carbon::today())['daily'];

        return [
            'sales_today' => (float) $salesToday,
            'profit_today' => (float) $profitToday,
            'pending_today' => $pendingToday,
            'success_ratio_today' => $successRatio,
            'best_seller_today' => $bestSeller?->product?->name,
            'best_seller_qty' => $bestSeller?->qty_sold,
            'last_7_days' => $last7Days,
        ];
    }

    /**
     * Laporan Sales & Revenue harian dalam rentang tanggal (inklusif).
     */
    public function salesRevenue(Carbon $from, Carbon $to): array
    {
        $rows = Order::selectRaw('DATE(created_at) as date, COUNT(*) as orders_count, SUM(price) as revenue')
            ->whereIn('status', self::REVENUE_STATUSES)
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $daily = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $key = $cursor->toDateString();
            $row = $rows->get($key);
            $daily[] = [
                'date' => $key,
                'orders_count' => $row->orders_count ?? 0,
                'revenue' => (float) ($row->revenue ?? 0),
            ];
            $cursor->addDay();
        }

        $refundTotal = Order::where('status', Order::STATUS_REFUNDED)
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->sum('price');

        return [
            'daily' => $daily,
            'total_orders' => array_sum(array_column($daily, 'orders_count')),
            'total_revenue' => array_sum(array_column($daily, 'revenue')),
            'total_refund' => (float) $refundTotal,
        ];
    }

    /**
     * Laporan Profit/Margin harian - hanya dari order berstatus success.
     */
    public function profitMargin(Carbon $from, Carbon $to): array
    {
        $rows = Order::selectRaw(
            'DATE(created_at) as date, '.
            'SUM(price) as revenue, '.
            'SUM(COALESCE(cost_price, 0)) as cost, '.
            'SUM(price - COALESCE(cost_price, 0)) as profit'
        )
            ->where('status', Order::STATUS_SUCCESS)
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $daily = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $key = $cursor->toDateString();
            $row = $rows->get($key);
            $revenue = (float) ($row->revenue ?? 0);
            $cost = (float) ($row->cost ?? 0);
            $profit = (float) ($row->profit ?? 0);
            $daily[] = [
                'date' => $key,
                'revenue' => $revenue,
                'cost' => $cost,
                'profit' => $profit,
                'margin_percent' => $revenue > 0 ? round(($profit / $revenue) * 100, 1) : null,
            ];
            $cursor->addDay();
        }

        $totalRevenue = array_sum(array_column($daily, 'revenue'));
        $totalCost = array_sum(array_column($daily, 'cost'));
        $totalProfit = array_sum(array_column($daily, 'profit'));

        return [
            'daily' => $daily,
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'total_profit' => $totalProfit,
            'total_margin_percent' => $totalRevenue > 0 ? round(($totalProfit / $totalRevenue) * 100, 1) : null,
        ];
    }

    /**
     * Performa tiap provider top up: order yang ditangani, rasio sukses,
     * dan jumlah log error/timeout dari API & Webhook Logs.
     */
    public function providerPerformance(Carbon $from, Carbon $to): array
    {
        $range = [$from->copy()->startOfDay(), $to->copy()->endOfDay()];

        $orderStats = Order::selectRaw(
            'provider_id, '.
            "COUNT(*) as total_orders, ".
            "SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success_count, ".
            "SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count"
        )
            ->whereNotNull('provider_id')
            ->whereBetween('created_at', $range)
            ->groupBy('provider_id')
            ->get()
            ->keyBy('provider_id');

        $logStats = ApiLog::selectRaw(
            'provider_id, '.
            "SUM(CASE WHEN type = 'error' THEN 1 ELSE 0 END) as error_count, ".
            "SUM(CASE WHEN type = 'timeout' THEN 1 ELSE 0 END) as timeout_count"
        )
            ->whereNotNull('provider_id')
            ->whereBetween('created_at', $range)
            ->groupBy('provider_id')
            ->get()
            ->keyBy('provider_id');

        return Provider::orderBy('priority')
            ->get()
            ->map(function (Provider $provider) use ($orderStats, $logStats) {
                $stat = $orderStats->get($provider->id);
                $log = $logStats->get($provider->id);
                $total = $stat->total_orders ?? 0;
                $success = $stat->success_count ?? 0;

                return [
                    'id' => $provider->id,
                    'name' => $provider->name,
                    'is_active' => $provider->is_active,
                    'priority' => $provider->priority,
                    'total_orders' => $total,
                    'success_count' => $success,
                    'failed_count' => $stat->failed_count ?? 0,
                    'success_rate' => $total > 0 ? round(($success / $total) * 100, 1) : null,
                    'error_count' => $log->error_count ?? 0,
                    'timeout_count' => $log->timeout_count ?? 0,
                ];
            })
            ->all();
    }

    /**
     * Performa tiap produk: jumlah terjual & revenue, diurutkan dari yang
     * paling laris. Dibatasi ke $limit produk teratas untuk halaman web,
     * tapi export CSV memakai limit yang lebih besar (lihat controller).
     */
    public function productPerformance(Carbon $from, Carbon $to, int $limit = 50): array
    {
        return Product::query()
            ->select('products.id', 'products.name', 'games.name as game_name')
            ->leftJoin('games', 'games.id', '=', 'products.game_id')
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN orders.status IN (?, ?, ?, ?) THEN orders.quantity ELSE 0 END), 0) as qty_sold, '.
                'COALESCE(SUM(CASE WHEN orders.status IN (?, ?, ?, ?) THEN orders.price ELSE 0 END), 0) as revenue, '.
                'COUNT(orders.id) as order_count',
                [...self::REVENUE_STATUSES, ...self::REVENUE_STATUSES]
            )
            ->leftJoin('orders', function ($join) use ($from, $to) {
                $join->on('orders.product_id', '=', 'products.id')
                    ->whereBetween('orders.created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);
            })
            ->groupBy('products.id', 'products.name', 'games.name')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'id' => $row->id,
                'name' => $row->name,
                'game_name' => $row->game_name,
                'qty_sold' => (int) $row->qty_sold,
                'revenue' => (float) $row->revenue,
                'order_count' => (int) $row->order_count,
            ])
            ->all();
    }
}