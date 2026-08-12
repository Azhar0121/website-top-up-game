<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $complaints = Complaint::query()
            ->status($request->get('status'))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.complaints.index', compact('complaints'));
    }

    public function show(Complaint $complaint)
    {
        return view('admin.complaints.show', compact('complaint'));
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        $validated = Validator::make($request->all(), [
            'status'     => ['required', 'in:open,in_progress,resolved'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ])->validate();

        $before = $complaint->only(['status', 'admin_note']);

        $complaint->update([
            'status'     => $validated['status'],
            'admin_note' => $validated['admin_note'] ?? $complaint->admin_note,
            'handled_by' => auth()->id(),
        ]);

        $changes = AuditLogService::diff($before, $complaint->only(['status', 'admin_note']));
        if (! empty($changes)) {
            AuditLogService::record(
                action: 'updated',
                description: "Memperbarui status keluhan \"{$complaint->subject}\".",
                subject: $complaint,
                changes: $changes,
            );
        }

        return back()->with('status', 'Status keluhan berhasil diperbarui.');
    }
}
