<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, ReportService $reportService)
    {
        $kpi = $reportService->todayKpi();

        $trendGranularity = in_array($request->query('trend'), ReportService::GRANULARITIES, true)
            ? $request->query('trend')
            : 'daily';

        $trendData = $reportService->salesTrend($trendGranularity);

        return view('admin.dashboard', compact('kpi', 'trendData', 'trendGranularity'));
    }
}