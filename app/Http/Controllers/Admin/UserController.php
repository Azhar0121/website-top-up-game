<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected array $allRoles = ['owner', 'admin', 'finance', 'cs', 'marketing', 'developer', 'customer'];

    /**
     * GET /admin/users
     */
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($q2) => $q2
                ->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%')
            ))
            ->when($request->filled('role'), fn ($q) => $q->whereHas('roles', fn ($q2) => $q2->where('name', $request->role)))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $roles = $this->allRoles;

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * GET /admin/users/{user}/edit
     */
    public function edit(User $user)
    {
        $roles = $this->allRoles;
        $currentRole = $user->roles->first()->name ?? 'customer';

        return view('admin.users.form', compact('user', 'roles', 'currentRole'));
    }

    /**
     * PUT /admin/users/{user}
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in($this->allRoles)],
        ]);

        $staffRoles = ['owner', 'admin', 'finance', 'cs', 'marketing', 'developer'];
        if ($user->id === auth()->id() && ! in_array($validated['role'], $staffRoles)) {
            return back()->with('error', 'Tidak bisa mengubah role akun kamu sendiri menjadi customer.');
        }

        $user->syncRoles([Role::firstOrCreate(['name' => $validated['role']])->name]);
        $user->update(['role' => $validated['role']]);

        return redirect()->route('admin.users.index')
            ->with('status', "Role {$user->name} berhasil diubah menjadi \"{$validated['role']}\".");
    }
}