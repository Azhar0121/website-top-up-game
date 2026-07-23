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
    protected array $staffRoles = ['owner', 'admin', 'finance', 'cs', 'marketing', 'developer'];

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
        $currentRoles = $user->roles->pluck('name')->toArray();

        return view('admin.users.form', compact('user', 'roles', 'currentRoles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles'   => 'required|array|min:1',
            'roles.*' => Rule::in($this->allRoles),
        ], [
            'roles.required' => 'Pilih minimal satu role.',
        ]);

        if ($user->id === auth()->id() && ! array_intersect($validated['roles'], $this->staffRoles)) {
            return back()->with('error', 'Tidak bisa mengubah role akun kamu sendiri sampai tidak ada role staff sama sekali.');
        }

        $roleNames = collect($validated['roles'])->map(fn ($name) => Role::firstOrCreate(['name' => $name])->name);
        $user->syncRoles($roleNames->all());

        return redirect()->route('admin.users.index')
            ->with('status', "Role {$user->name} berhasil diubah menjadi: ".$roleNames->implode(', ').'.');
    }

    public function bulkUpdateRole(Request $request)
    {
        $validated = $request->validate([
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'role'       => ['required', Rule::in($this->allRoles)],
        ], [
            'user_ids.required' => 'Pilih minimal satu user dulu.',
        ]);

        $role = Role::firstOrCreate(['name' => $validated['role']])->name;

        $users = User::whereIn('id', $validated['user_ids'])->get();
        foreach ($users as $user) {
            $user->assignRole($role);
        }

        return redirect()->route('admin.users.index')
            ->with('status', "Role \"{$role}\" berhasil ditambahkan ke {$users->count()} user.");
    }
}