<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $vouchers = Voucher::when($request->filled('search'), fn ($q) => $q->where('code', 'like', '%'.strtoupper($request->search).'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        $voucher = new Voucher(['is_active' => true]);

        return view('admin.vouchers.form', compact('voucher'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateVoucher($request);
        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active');

        $voucher = Voucher::create($validated);

        AuditLogService::record(
            action: 'created',
            description: "Membuat voucher \"{$voucher->code}\".",
            subject: $voucher,
        );

        return redirect()->route('admin.vouchers.index')
            ->with('status', "Voucher \"{$voucher->code}\" berhasil dibuat.");
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.form', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $validated = $this->validateVoucher($request, $voucher->id);
        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active');

        $before = $voucher->only(array_keys($validated));
        $voucher->update($validated);
        $changes = AuditLogService::diff($before, $voucher->only(array_keys($validated)));

        if (! empty($changes)) {
            AuditLogService::record(
                action: 'updated',
                description: "Mengedit voucher \"{$voucher->code}\".",
                subject: $voucher,
                changes: $changes,
            );
        }

        return redirect()->route('admin.vouchers.index')
            ->with('status', "Voucher \"{$voucher->code}\" berhasil diupdate.");
    }

    public function destroy(Voucher $voucher)
    {
        $code = $voucher->code;
        $voucher->delete();

        AuditLogService::record(
            action: 'deleted',
            description: "Menghapus voucher \"{$code}\".",
            subject: $voucher,
        );

        return back()->with('status', "Voucher \"{$code}\" berhasil dihapus.");
    }

    private function validateVoucher(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code'            => 'required|string|max:30|unique:vouchers,code'.($ignoreId ? ",{$ignoreId}" : ''),
            'type'            => ['required', Rule::in(['fixed', 'percentage'])],
            'value'           => 'required|numeric|min:0',
            'max_discount'    => 'nullable|numeric|min:0',
            'min_transaction' => 'nullable|numeric|min:0',
            'usage_limit'     => 'nullable|integer|min:1',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
        ], [
            'end_date.after_or_equal' => 'Tanggal berakhir tidak boleh sebelum tanggal mulai.',
        ]);
    }
}