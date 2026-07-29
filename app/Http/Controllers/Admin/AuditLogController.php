<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    protected array $staffRoles = ['owner', 'admin', 'finance', 'cs', 'marketing', 'developer'];

    /**
     * GET /admin/audit-logs
     * PRD 4.6: "Audit Log: Merekam siapa admin yang melakukan Login, Edit Produk,
     * Edit Margin, Action Refund, dan Hapus Data (lengkap dengan timestamp & IP)."
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'action'  => ['nullable', 'string'],
            'user_id' => ['nullable', 'integer'],
            'search'  => ['nullable', 'string', 'max:100'],
            'from'    => ['nullable', 'date'],
            'to'      => ['nullable', 'date'],
        ]);

        $logs = AuditLog::with('user')
            ->when($validated['action'] ?? null, fn ($q, $action) => $q->where('action', $action))
            ->when($validated['user_id'] ?? null, fn ($q, $userId) => $q->where('user_id', $userId))
            ->when($validated['search'] ?? null, fn ($q, $search) => $q->where('description', 'like', "%{$search}%"))
            ->when($validated['from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($validated['to'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $actionOptions = [
            'login'            => 'Login',
            'created'          => 'Tambah Data',
            'updated'          => 'Edit Data',
            'deleted'          => 'Hapus Data',
            'force_success'    => 'Force Success',
            'status_override'  => 'Override Status',
            'role_updated'     => 'Ubah Role',
        ];

        $staffUsers = User::role($this->staffRoles)->orderBy('name')->get(['id', 'name']);

        return view('admin.audit-logs.index', [
            'logs'          => $logs,
            'actionOptions' => $actionOptions,
            'staffUsers'    => $staffUsers,
            'filters'       => $validated,
        ]);
    }
}
