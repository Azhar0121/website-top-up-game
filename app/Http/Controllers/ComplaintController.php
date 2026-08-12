<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    public function create()
    {
        return view('customer.complaint-form', [
            'user' => auth()->user(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
            'image'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [], [
            'name' => 'nama',
            'message' => 'keluhan',
        ])->validate();

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('complaints', 'public');
        }
        unset($validated['image']);

        $validated['user_id'] = auth()->id();

        Complaint::create($validated);

        return redirect()->route('contact')
            ->with('status', 'Keluhan kamu berhasil dikirim. Tim CS kami akan segera menindaklanjuti lewat email yang kamu daftarkan.');
    }
}
