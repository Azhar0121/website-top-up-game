<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class CmsPageController extends Controller
{
    /**
     * Slug dibatasi ke 2 nilai tetap ini - lihat catatan di migration pages.
     * Kalau nanti mau nambah halaman statis lain, tambahkan di sini + seed-nya.
     */
    private const ALLOWED_SLUGS = ['terms', 'privacy'];

    public function index()
    {
        $pages = collect(self::ALLOWED_SLUGS)->map(
            fn ($slug) => Page::firstOrNew(['slug' => $slug], ['title' => ucfirst($slug), 'content' => ''])
        );

        return view('admin.pages.index', compact('pages'));
    }

    public function edit(string $slug)
    {
        abort_unless(in_array($slug, self::ALLOWED_SLUGS, true), 404);

        $page = Page::firstOrNew(['slug' => $slug], [
            'title' => $slug === 'terms' ? 'Syarat & Ketentuan' : 'Kebijakan Privasi',
            'content' => '',
        ]);

        return view('admin.pages.form', compact('page'));
    }

    public function update(Request $request, string $slug)
    {
        abort_unless(in_array($slug, self::ALLOWED_SLUGS, true), 404);

        $validated = $request->validate([
            'title'   => ['required', 'string', 'max:150'],
            'content' => ['required', 'string'],
        ]);

        $page = Page::firstOrNew(['slug' => $slug]);
        $isNew = ! $page->exists;
        $before = $page->only(['title', 'content']);

        $page->fill($validated + ['slug' => $slug])->save();

        $changes = AuditLogService::diff($before, $page->only(['title', 'content']));

        if (! empty($changes)) {
            AuditLogService::record(
                action: $isNew ? 'created' : 'updated',
                description: ($isNew ? 'Membuat' : 'Mengedit').' halaman "'.$page->title.'".',
                subject: $page,
                changes: $changes,
            );
        }

        return redirect()->route('admin.pages.index')
            ->with('status', "Halaman \"{$page->title}\" berhasil disimpan.");
    }
}
