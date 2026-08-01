<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\Game;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FlashSaleController extends Controller
{
    public function index()
    {
        $flashSales = FlashSale::withCount('products')->orderByDesc('start_at')->get();

        return view('admin.flash-sales.index', compact('flashSales'));
    }

    public function create()
    {
        $flashSale = new FlashSale();
        $games = $this->gamesWithProducts();

        return view('admin.flash-sales.form', compact('flashSale', 'games'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateFlashSale($request);
        $validated['is_active'] = $request->boolean('is_active', true);
        $productIds = $validated['product_ids'];
        unset($validated['product_ids']);

        $flashSale = FlashSale::create($validated);
        $flashSale->products()->sync($productIds);

        AuditLogService::record(
            action: 'created',
            description: "Membuat flash sale \"{$flashSale->name}\" ({$flashSale->discount_type} {$flashSale->discount_value}) untuk ".count($productIds).' produk.',
            subject: $flashSale,
        );

        return redirect()->route('admin.flash-sales.index')
            ->with('status', "Flash sale \"{$flashSale->name}\" berhasil dibuat.");
    }

    public function edit(FlashSale $flashSale)
    {
        $games = $this->gamesWithProducts();
        $selectedProductIds = $flashSale->products()->pluck('products.id')->all();

        return view('admin.flash-sales.form', compact('flashSale', 'games', 'selectedProductIds'));
    }

    public function update(Request $request, FlashSale $flashSale)
    {
        $validated = $this->validateFlashSale($request);
        $validated['is_active'] = $request->boolean('is_active');
        $productIds = $validated['product_ids'];
        unset($validated['product_ids']);

        $trackedFields = ['name', 'discount_type', 'discount_value', 'start_at', 'end_at', 'is_active'];
        $before = $flashSale->only($trackedFields);
        $beforeProductIds = $flashSale->products()->pluck('products.id')->sort()->values()->all();

        $flashSale->update($validated);
        $flashSale->products()->sync($productIds);

        $changes = AuditLogService::diff($before, $flashSale->only($trackedFields));

        $afterProductIds = collect($productIds)->sort()->values()->all();
        if ($beforeProductIds !== $afterProductIds) {
            $changes['product_ids'] = ['old' => $beforeProductIds, 'new' => $afterProductIds];
        }

        if (! empty($changes)) {
            AuditLogService::record(
                action: 'updated',
                description: "Mengedit flash sale \"{$flashSale->name}\".",
                subject: $flashSale,
                changes: $changes,
            );
        }

        return redirect()->route('admin.flash-sales.index')
            ->with('status', "Flash sale \"{$flashSale->name}\" berhasil diupdate.");
    }

    public function destroy(FlashSale $flashSale)
    {
        $name = $flashSale->name;
        $flashSale->delete();

        AuditLogService::record(
            action: 'deleted',
            description: "Menghapus flash sale \"{$name}\".",
            subject: $flashSale,
        );

        return back()->with('status', "Flash sale \"{$name}\" berhasil dihapus.");
    }

    public function toggle(FlashSale $flashSale)
    {
        $wasActive = $flashSale->is_active;
        $flashSale->update(['is_active' => ! $wasActive]);

        AuditLogService::record(
            action: 'updated',
            description: 'Flash sale "'.$flashSale->name.'" '.($flashSale->is_active ? 'diaktifkan' : 'dinonaktifkan').'.',
            subject: $flashSale,
            changes: ['is_active' => ['old' => $wasActive, 'new' => $flashSale->is_active]],
        );

        return back()->with('status', 'Flash sale berhasil '.($flashSale->is_active ? 'diaktifkan' : 'dinonaktifkan').'.');
    }

    private function validateFlashSale(Request $request): array
    {
        return Validator::make($request->all(), [
            'name'           => ['required', 'string', 'max:150'],
            'discount_type'  => ['required', 'in:fixed,percentage'],
            'discount_value' => [
                'required',
                'numeric',
                'min:0.01',
                $request->input('discount_type') === 'percentage' ? 'max:100' : 'max:999999999',
            ],
            'start_at'       => ['required', 'date'],
            'end_at'         => ['required', 'date', 'after:start_at'],
            'product_ids'    => ['required', 'array', 'min:1'],
            'product_ids.*'  => ['exists:products,id'],
        ], [
            'end_at.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'discount_value.max' => 'Diskon persentase tidak boleh lebih dari 100%.',
            'product_ids.required' => 'Pilih minimal satu produk yang kena flash sale.',
        ])->validate();
    }

    /**
     * @return \Illuminate\Support\Collection<int, Game>
     */
    private function gamesWithProducts()
    {
        return Game::with(['products' => fn ($q) => $q->where('is_active', true)->orderBy('name')])
            ->whereHas('products', fn ($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->get();
    }
}
