<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->orderByDesc('created_at')->get();

        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        $banner = new Banner();

        return view('admin.banners.form', compact('banner'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateBanner($request);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['image'] = $request->file('image')->store('banners', 'public');
        $validated['is_active'] = $request->boolean('is_active', true);

        $banner = Banner::create($validated);

        AuditLogService::record(
            action: 'created',
            description: "Menambahkan banner \"{$banner->title}\".",
            subject: $banner,
        );

        return redirect()->route('admin.banners.index')
            ->with('status', 'Banner baru berhasil ditambahkan.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.form', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $this->validateBanner($request, $banner->id);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($banner->image);
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        $trackedFields = ['title', 'link_url', 'sort_order', 'is_active'];
        $before = $banner->only($trackedFields);

        $banner->update($validated);

        $changes = AuditLogService::diff($before, $banner->only($trackedFields));
        if ($request->hasFile('image')) {
            $changes['image'] = ['old' => 'diganti', 'new' => 'diganti'];
        }

        if (! empty($changes)) {
            AuditLogService::record(
                action: 'updated',
                description: "Mengedit banner \"{$banner->title}\".",
                subject: $banner,
                changes: $changes,
            );
        }

        return redirect()->route('admin.banners.index')
            ->with('status', "Banner \"{$banner->title}\" berhasil diupdate.");
    }

    public function destroy(Banner $banner)
    {
        $title = $banner->title;
        Storage::disk('public')->delete($banner->image);
        $banner->delete();

        AuditLogService::record(
            action: 'deleted',
            description: "Menghapus banner \"{$title}\".",
            subject: $banner,
        );

        return back()->with('status', "Banner \"{$title}\" berhasil dihapus.");
    }

    /**
     * POST /admin/banners/{banner}/toggle
     * Sama seperti Control Switch di Provider - 1 klik langsung dari daftar.
     */
    public function toggle(Banner $banner)
    {
        $wasActive = $banner->is_active;
        $banner->update(['is_active' => ! $wasActive]);

        AuditLogService::record(
            action: 'updated',
            description: 'Banner "'.$banner->title.'" '.($banner->is_active ? 'diaktifkan' : 'dinonaktifkan').'.',
            subject: $banner,
            changes: ['is_active' => ['old' => $wasActive, 'new' => $banner->is_active]],
        );

        return back()->with('status', 'Banner berhasil '.($banner->is_active ? 'diaktifkan' : 'dinonaktifkan').'.');
    }

    private function validateBanner(Request $request, ?int $ignoreId = null): array
    {
        return Validator::make($request->all(), [
            'title'      => ['required', 'string', 'max:150'],
            'image'      => [$ignoreId ? 'nullable' : 'required', 'image', 'max:2048'],
            'link_url'   => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ])->validate();
    }
}
