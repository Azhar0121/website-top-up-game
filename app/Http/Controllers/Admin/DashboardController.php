<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;

class DashboardController extends Controller
{
    public function index(ReportService $reportService)
    {
        $kpi = $reportService->todayKpi();

        return view('admin.dashboard', compact('kpi'));
    }
}
