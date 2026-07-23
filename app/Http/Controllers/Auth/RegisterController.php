<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            return redirect()->route('account.index');
        }

        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:150|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'email.unique' => 'Email ini sudah terdaftar. Coba masuk (login) saja.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole(\Spatie\Permission\Models\Role::firstOrCreate(['name' => 'customer'])->name);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('account.index')
            ->with('status', 'Akun berhasil dibuat. Selamat datang, '.$user->name.'!');
    }
}