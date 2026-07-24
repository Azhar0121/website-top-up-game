<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiLog;
use App\Models\Provider;
use Illuminate\Http\Request;

class ApiLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ApiLog::with(['provider', 'order'])
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('provider_id'), fn ($q) => $q->where('provider_id', $request->provider_id))
            ->when($request->filled('search'), fn ($q) => $q->whereHas('order', fn ($q2) =>
                $q2->where('invoice_number', 'like', '%'.$request->search.'%')
            ))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $providers = Provider::orderBy('name')->get(['id', 'name']);
        $typeCounts = ApiLog::selectRaw('type, count(*) as total')->groupBy('type')->pluck('total', 'type');

        return view('admin.api-logs.index', compact('logs', 'providers', 'typeCounts'));
    }

    public function show(ApiLog $apiLog)
    {
        $apiLog->load(['provider', 'order']);

        return view('admin.api-logs.show', compact('apiLog'));
    }
}