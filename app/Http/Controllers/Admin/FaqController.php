<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('sort_order')->orderByDesc('created_at')->get();

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        $faq = new Faq();

        return view('admin.faqs.form', compact('faq'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateFaq($request);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active', true);

        $faq = Faq::create($validated);

        AuditLogService::record(
            action: 'created',
            description: "Menambahkan FAQ \"{$faq->question}\".",
            subject: $faq,
        );

        return redirect()->route('admin.faqs.index')
            ->with('status', 'FAQ baru berhasil ditambahkan.');
    }

    public function edit(Faq $faq)
    {
        return view('admin.faqs.form', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $this->validateFaq($request);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active');

        $before = $faq->only(['question', 'answer', 'sort_order', 'is_active']);
        $faq->update($validated);
        $changes = AuditLogService::diff($before, $faq->only(['question', 'answer', 'sort_order', 'is_active']));

        if (! empty($changes)) {
            AuditLogService::record(
                action: 'updated',
                description: "Mengedit FAQ \"{$faq->question}\".",
                subject: $faq,
                changes: $changes,
            );
        }

        return redirect()->route('admin.faqs.index')
            ->with('status', 'FAQ berhasil diupdate.');
    }

    public function destroy(Faq $faq)
    {
        $question = $faq->question;
        $faq->delete();

        AuditLogService::record(
            action: 'deleted',
            description: "Menghapus FAQ \"{$question}\".",
            subject: $faq,
        );

        return back()->with('status', 'FAQ berhasil dihapus.');
    }

    private function validateFaq(Request $request): array
    {
        return Validator::make($request->all(), [
            'question'   => ['required', 'string', 'max:255'],
            'answer'     => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ])->validate();
    }
}
