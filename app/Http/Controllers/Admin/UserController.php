<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
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
     * GET /admin/users/create
     */
    public function create()
    {
        $roles = $this->allRoles;

        return view('admin.users.create', compact('roles'));
    }

    /**
     * POST /admin/users
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:150|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'roles'    => 'required|array|min:1',
            'roles.*'  => Rule::in($this->allRoles),
        ], [
            'email.unique'   => 'Email ini sudah dipakai user lain.',
            'roles.required' => 'Pilih minimal satu role.',
        ]);

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'email_verified_at' => now(), // dibuat langsung oleh admin, tidak perlu verifikasi email
        ]);

        $roleNames = collect($validated['roles'])->map(fn ($name) => Role::firstOrCreate(['name' => $name])->name);
        $user->syncRoles($roleNames->all());

        AuditLogService::record(
            action: 'created',
            description: "Menambahkan user baru \"{$user->name}\" ({$user->email}) dengan role: ".$roleNames->implode(', ').'.',
            subject: $user,
        );

        return redirect()->route('admin.users.index')
            ->with('status', "User \"{$user->name}\" berhasil ditambahkan.");
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

        $before = $user->roles->pluck('name')->all();
        $user->syncRoles($roleNames->all());

        AuditLogService::record(
            action: 'role_updated',
            description: "Mengubah role \"{$user->name}\" ({$user->email}).",
            subject: $user,
            changes: ['roles' => ['old' => $before, 'new' => $roleNames->all()]],
        );

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

        AuditLogService::record(
            action: 'role_updated',
            description: "Menambahkan role \"{$role}\" ke {$users->count()} user sekaligus: ".$users->pluck('name')->implode(', ').'.',
            changes: ['role_added' => $role, 'user_ids' => $validated['user_ids']],
        );

        return redirect()->route('admin.users.index')
            ->with('status', "Role \"{$role}\" berhasil ditambahkan ke {$users->count()} user.");
    }

    /**
     * POST /admin/users/{user}/block
     */
    public function block(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa memblokir akun kamu sendiri.');
        }

        if ($user->hasRole('owner') && User::role('owner')->where('is_blocked', false)->count() <= 1) {
            return back()->with('error', 'Tidak bisa memblokir owner terakhir yang masih aktif.');
        }

        $validated = $request->validate([
            'blocked_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'is_blocked'     => true,
            'blocked_at'     => now(),
            'blocked_reason' => $validated['blocked_reason'] ?? null,
        ]);

        DB::table('sessions')->where('user_id', $user->id)->delete();

        AuditLogService::record(
            action: 'updated',
            description: "Memblokir user \"{$user->name}\" ({$user->email})".($validated['blocked_reason'] ? '. Alasan: '.$validated['blocked_reason'] : '.'),
            subject: $user,
            changes: ['is_blocked' => ['old' => false, 'new' => true]],
        );

        return back()->with('status', "User \"{$user->name}\" berhasil diblokir.");
    }

    /**
     * POST /admin/users/{user}/unblock
     */
    public function unblock(User $user)
    {
        $user->update([
            'is_blocked'     => false,
            'blocked_at'     => null,
            'blocked_reason' => null,
        ]);

        AuditLogService::record(
            action: 'updated',
            description: "Membuka blokir user \"{$user->name}\" ({$user->email}).",
            subject: $user,
            changes: ['is_blocked' => ['old' => true, 'new' => false]],
        );

        return back()->with('status', "User \"{$user->name}\" berhasil dibuka blokirnya.");
    }
}