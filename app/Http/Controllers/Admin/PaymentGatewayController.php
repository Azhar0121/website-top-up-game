<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentGatewayController extends Controller
{
    /**
     * Gateway yang benar-benar diimplementasikan di PaymentGatewayServiceFactory.
     * Kalau admin toggle aktif gateway di luar daftar ini, tampilkan peringatan -
     * checkout bakal error kalau sampai ada customer yang pilih gateway itu.
     */
    private const IMPLEMENTED_CODES = ['midtrans', 'tripay'];

    /**
     * Kredensial Midtrans SENGAJA tidak dikelola dari sini - MidtransService baca
     * langsung dari .env lewat config('midtrans.*') (keputusan arsitektur dari awal
     * project). Form di sini cuma boleh ubah status aktif/nonaktif & mode sandbox
     * untuk Midtrans, bukan kredensialnya.
     */
    private const ENV_MANAGED_CODES = ['midtrans'];

    public function index()
    {
        $gateways = PaymentGateway::orderByRaw("code = 'midtrans' desc")->orderBy('name')->get();

        return view('admin.payment-gateways.index', [
            'gateways' => $gateways,
            'implementedCodes' => self::IMPLEMENTED_CODES,
        ]);
    }

    public function edit(PaymentGateway $paymentGateway)
    {
        return view('admin.payment-gateways.form', [
            'gateway' => $paymentGateway,
            'isEnvManaged' => in_array($paymentGateway->code, self::ENV_MANAGED_CODES, true),
            'isImplemented' => in_array($paymentGateway->code, self::IMPLEMENTED_CODES, true),
        ]);
    }

    public function update(Request $request, PaymentGateway $paymentGateway)
    {
        $isEnvManaged = in_array($paymentGateway->code, self::ENV_MANAGED_CODES, true);

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'is_sandbox'    => ['nullable'],
            'is_active'     => ['nullable'],
            'merchant_code' => ['nullable', 'string', 'max:100'],
            'api_key'       => ['nullable', 'string', 'max:255'],
            'api_secret'    => ['nullable', 'string', 'max:255'],
        ]);

        $validated['is_sandbox'] = $request->boolean('is_sandbox');
        $validated['is_active'] = $request->boolean('is_active');

        // Kredensial gateway yang env-managed (Midtrans) tidak boleh diubah dari sini sama sekali.
        if ($isEnvManaged) {
            unset($validated['merchant_code'], $validated['api_key'], $validated['api_secret']);
        } else {
            $credentialChanged = $request->filled('api_key') || $request->filled('api_secret') || $request->filled('merchant_code');

            foreach (['merchant_code', 'api_key', 'api_secret'] as $field) {
                if (! $request->filled($field)) {
                    unset($validated[$field]);
                }
            }
        }

        $trackedFields = ['name', 'is_sandbox', 'is_active'];
        $before = $paymentGateway->only($trackedFields);
        $wasActive = $paymentGateway->is_active;

        $paymentGateway->update($validated);

        $changes = AuditLogService::diff($before, $paymentGateway->only($trackedFields));
        if (! $isEnvManaged && isset($credentialChanged) && $credentialChanged) {
            $changes['credentials'] = ['old' => '(disembunyikan)', 'new' => 'diperbarui'];
        }

        if (! empty($changes)) {
            AuditLogService::record(
                action: 'updated',
                description: 'Mengedit payment gateway "'.$paymentGateway->name.'".',
                subject: $paymentGateway,
                changes: $changes,
            );
        }

        // Peringatan kalau admin baru saja MENGAKTIFKAN gateway yang belum ada
        // implementasi service-nya - checkout bisa error kalau ada yang pilih ini.
        if (! $wasActive && $paymentGateway->is_active && ! in_array($paymentGateway->code, self::IMPLEMENTED_CODES, true)) {
            return redirect()->route('admin.payment-gateways.index')
                ->with('warning', "\"{$paymentGateway->name}\" diaktifkan, tapi service-nya belum terhubung ke PaymentGatewayServiceFactory. Checkout akan gagal kalau ada customer yang pilih gateway ini.");
        }

        return redirect()->route('admin.payment-gateways.index')
            ->with('status', "Payment gateway \"{$paymentGateway->name}\" berhasil diupdate.");
    }

    public function toggle(PaymentGateway $paymentGateway)
    {
        $wasActive = $paymentGateway->is_active;
        $paymentGateway->update(['is_active' => ! $wasActive]);

        AuditLogService::record(
            action: 'updated',
            description: $paymentGateway->name.' '.($paymentGateway->is_active ? 'diaktifkan' : 'dinonaktifkan').'.',
            subject: $paymentGateway,
            changes: ['is_active' => ['old' => $wasActive, 'new' => $paymentGateway->is_active]],
        );

        if ($paymentGateway->is_active && ! in_array($paymentGateway->code, self::IMPLEMENTED_CODES, true)) {
            return back()->with('warning', "\"{$paymentGateway->name}\" diaktifkan, tapi service-nya belum terhubung ke PaymentGatewayServiceFactory. Checkout akan gagal kalau ada customer yang pilih gateway ini.");
        }

        return back()->with('status', $paymentGateway->name.' berhasil '.($paymentGateway->is_active ? 'diaktifkan' : 'dinonaktifkan').'.');
    }
}