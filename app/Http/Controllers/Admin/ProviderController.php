<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

/**
 * "Manajemen Provider & API" + sitemap "Providers & API > Provider List & Priority":
 * - Multi-Provider: daftar semua provider top up
 * - Control Switch: enable/disable real-time
 * - Priority Routing: atur urutan prioritas (dipakai OrderService buat auto-failover)
 *
 * Kredensial (api_key/api_secret) di kolom database pakai cast 'encrypted' - makanya
 * WAJIB update lewat instance model ($provider->update()), BUKAN DB::table()->update()
 * yang akan skip proses enkripsi otomatis itu.
 */
class ProviderController extends Controller
{
    public function index()
    {
        $providers = Provider::orderBy('priority')->get();

        return view('admin.providers.index', compact('providers'));
    }

    public function create()
    {
        $provider = new Provider(['priority' => (Provider::max('priority') ?? 0) + 1, 'is_active' => true]);

        return view('admin.providers.form', compact('provider'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProvider($request);
        $validated['is_active'] = $request->boolean('is_active');

        $provider = Provider::create($validated);

        AuditLogService::record(
            action: 'created',
            description: "Menambahkan provider \"{$provider->name}\" (prioritas {$provider->priority}).",
            subject: $provider,
        );

        return redirect()->route('admin.providers.index')
            ->with('status', "Provider \"{$provider->name}\" berhasil ditambahkan.");
    }

    public function edit(Provider $provider)
    {
        return view('admin.providers.form', compact('provider'));
    }

    public function update(Request $request, Provider $provider)
    {
        $validated = $this->validateProvider($request, $provider->id);
        $validated['is_active'] = $request->boolean('is_active');

        // api_key/api_secret sengaja OPSIONAL saat edit - kalau field-nya dikosongkan,
        // kredensial yang sudah tersimpan TIDAK ditimpa jadi kosong.
        $credentialChanged = $request->filled('api_key') || $request->filled('api_secret');

        if (! $request->filled('api_key')) {
            unset($validated['api_key']);
        }
        if (! $request->filled('api_secret')) {
            unset($validated['api_secret']);
        }

        // Field kredensial SENGAJA tidak pernah ikut di-diff/disimpan ke audit log
        $trackedFields = ['name', 'code', 'base_url', 'priority', 'is_active'];
        $before = $provider->only($trackedFields);

        $provider->update($validated);

        $changes = AuditLogService::diff($before, $provider->only($trackedFields));

        if ($credentialChanged) {
            $changes['credentials'] = ['old' => '(disembunyikan)', 'new' => 'diperbarui'];
        }

        if (! empty($changes)) {
            AuditLogService::record(
                action: 'updated',
                description: 'Mengedit provider "'.$provider->name.'"'.($credentialChanged ? ' (termasuk update kredensial API).' : '.'),
                subject: $provider,
                changes: $changes,
            );
        }

        return redirect()->route('admin.providers.index')
            ->with('status', "Provider \"{$provider->name}\" berhasil diupdate.");
    }

    /**
     * POST /admin/providers/{provider}/toggle
     * Control Switch enable/disable real-time
     */
    public function toggle(Provider $provider)
    {
        $wasActive = $provider->is_active;
        $provider->update(['is_active' => ! $wasActive]);

        AuditLogService::record(
            action: 'updated',
            description: $provider->name.' berhasil '.($provider->is_active ? 'diaktifkan' : 'dinonaktifkan').'.',
            subject: $provider,
            changes: ['is_active' => ['old' => $wasActive, 'new' => $provider->is_active]],
        );

        return back()->with('status', $provider->name.' berhasil '.($provider->is_active ? 'diaktifkan' : 'dinonaktifkan').'.');
    }

    private function validateProvider(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'       => 'required|string|max:100',
            'code'       => 'required|string|max:50|unique:providers,code'.($ignoreId ? ",{$ignoreId}" : ''),
            'base_url'   => 'required|url|max:255',
            'api_key'    => 'nullable|string',
            'api_secret' => 'nullable|string',
            'priority'   => 'required|integer|min:1',
        ]);
    }
}