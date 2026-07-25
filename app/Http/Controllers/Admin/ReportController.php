<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function salesRevenue(Request $request, ReportService $reportService)
    {
        [$from, $to] = $this->dateRange($request);
        $report = $reportService->salesRevenue($from, $to);

        return view('admin.reports.sales-revenue', compact('report', 'from', 'to'));
    }

    public function exportSalesRevenue(Request $request, ReportService $reportService)
    {
        [$from, $to] = $this->dateRange($request);
        $report = $reportService->salesRevenue($from, $to);

        return $this->streamCsv("sales-revenue_{$from->toDateString()}_{$to->toDateString()}.csv",
            ['Tanggal', 'Jumlah Order', 'Revenue (Rp)'],
            collect($report['daily'])->map(fn ($row) => [$row['date'], $row['orders_count'], $row['revenue']])
        );
    }

    public function profitMargin(Request $request, ReportService $reportService)
    {
        [$from, $to] = $this->dateRange($request);
        $report = $reportService->profitMargin($from, $to);

        return view('admin.reports.profit-margin', compact('report', 'from', 'to'));
    }

    public function exportProfitMargin(Request $request, ReportService $reportService)
    {
        [$from, $to] = $this->dateRange($request);
        $report = $reportService->profitMargin($from, $to);

        return $this->streamCsv("profit-margin_{$from->toDateString()}_{$to->toDateString()}.csv",
            ['Tanggal', 'Revenue (Rp)', 'Cost (Rp)', 'Profit (Rp)', 'Margin (%)'],
            collect($report['daily'])->map(fn ($row) => [
                $row['date'], $row['revenue'], $row['cost'], $row['profit'], $row['margin_percent'] ?? '-',
            ])
        );
    }

    public function providerPerformance(Request $request, ReportService $reportService)
    {
        [$from, $to] = $this->dateRange($request);
        $report = $reportService->providerPerformance($from, $to);

        return view('admin.reports.provider-performance', compact('report', 'from', 'to'));
    }

    public function exportProviderPerformance(Request $request, ReportService $reportService)
    {
        [$from, $to] = $this->dateRange($request);
        $report = $reportService->providerPerformance($from, $to);

        return $this->streamCsv("provider-performance_{$from->toDateString()}_{$to->toDateString()}.csv",
            ['Provider', 'Status', 'Total Order', 'Success', 'Failed', 'Success Rate (%)', 'Error Log', 'Timeout Log'],
            collect($report)->map(fn ($row) => [
                $row['name'], $row['is_active'] ? 'Aktif' : 'Nonaktif', $row['total_orders'],
                $row['success_count'], $row['failed_count'], $row['success_rate'] ?? '-',
                $row['error_count'], $row['timeout_count'],
            ])
        );
    }

    public function productPerformance(Request $request, ReportService $reportService)
    {
        [$from, $to] = $this->dateRange($request);
        $report = $reportService->productPerformance($from, $to);

        return view('admin.reports.product-performance', compact('report', 'from', 'to'));
    }

    public function exportProductPerformance(Request $request, ReportService $reportService)
    {
        [$from, $to] = $this->dateRange($request);
        // Export tidak dibatasi 50 seperti tampilan web, biar lengkap.
        $report = $reportService->productPerformance($from, $to, 100000);

        return $this->streamCsv("product-performance_{$from->toDateString()}_{$to->toDateString()}.csv",
            ['Produk', 'Game', 'Qty Terjual', 'Revenue (Rp)', 'Jumlah Order'],
            collect($report)->map(fn ($row) => [
                $row['name'], $row['game_name'] ?? '-', $row['qty_sold'], $row['revenue'], $row['order_count'],
            ])
        );
    }

    /**
     * Ambil & validasi rentang tanggal dari query string (?from=&to=).
     * Default: 30 hari terakhir termasuk hari ini.
     */
    private function dateRange(Request $request): array
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $to = isset($validated['to']) ? Carbon::parse($validated['to'])->startOfDay() : Carbon::today();
        $from = isset($validated['from']) ? Carbon::parse($validated['from'])->startOfDay() : $to->copy()->subDays(29);

        // Jaga-jaga kalau user isi "from" lebih baru dari "to" - tukar saja urutannya.
        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        return [$from, $to];
    }

    /**
     * Helper kecil buat streaming CSV supaya tidak perlu library tambahan
     * (Laravel + PHP native fputcsv sudah cukup untuk kebutuhan export ini).
     */
    private function streamCsv(string $filename, array $header, \Illuminate\Support\Collection $rows)
    {
        return response()->streamDownload(function () use ($header, $rows) {
            $handle = fopen('php://output', 'w');
            // BOM biar Excel baca UTF-8 dengan benar (karakter "Rp" dll tidak berantakan).
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $header);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
