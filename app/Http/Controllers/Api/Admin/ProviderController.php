<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProviderController extends Controller
{
    public function index()
    {
        $providers = Provider::orderBy('priority')->get([
            'id', 'name', 'code', 'priority', 'is_active', 'created_at',
        ]);

        return response()->json(['success' => true, 'data' => $providers]);
    }

    public function toggle(Provider $provider)
    {
        $provider->update(['is_active' => ! $provider->is_active]);

        return response()->json([
            'success' => true,
            'message' => $provider->is_active ? 'Provider diaktifkan' : 'Provider dinonaktifkan',
            'data' => $provider,
        ]);
    }

    public function updatePriority(Provider $provider, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'priority' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $provider->update(['priority' => $request->priority]);

        return response()->json(['success' => true, 'data' => $provider]);
    }
}