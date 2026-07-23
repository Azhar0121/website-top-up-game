<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * PRD 3 "Marketing & Konversi" (input kode promo saat checkout) + sitemap "Content &
 * Marketing (CMS) > Voucher & Promo Code". Logic validasi kode voucher sudah ada di
 * VoucherService (dipakai checkout) - controller ini cuma CRUD datanya lewat dashboard,
 * sebelumnya cuma bisa dibuat manual lewat seeder/tinker.
 */
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

        $voucher->update($validated);

        return redirect()->route('admin.vouchers.index')
            ->with('status', "Voucher \"{$voucher->code}\" berhasil diupdate.");
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();

        return back()->with('status', "Voucher \"{$voucher->code}\" berhasil dihapus.");
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
