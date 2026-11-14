<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends AdminController
{
    public function index()
    {
        return view('admin.users.index', ['users' => User::with('role')->orderBy('name')->get(), 'roles' => Role::withCount('users')->get()]);
    }

    public function create()
    {
        return view('admin.users.form', ['user' => new User(['is_active' => true]), 'roles' => Role::all()]);
    }

    public function store(Request $request)
    {
        $user = User::create($this->validated($request));
        $this->log('create', "Created user {$user->email}", $user);

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', ['user' => $user, 'roles' => Role::all()]);
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);
        if ($user->id === auth()->id()) {
            unset($data['role_id'], $data['is_active']); // cannot demote/deactivate yourself
        }
        $user->update($data);
        $this->log('update', "Updated user {$user->email}", $user);

        return redirect()->route('admin.users.index')->with('success', 'User saved.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        if ($user->isSuperAdmin() && User::whereHas('role', fn ($q) => $q->where('slug', 'super-admin'))->count() <= 1) {
            return back()->with('error', 'At least one Super Admin is required.');
        }
        $user->delete();
        $this->log('delete', "Deleted user {$user->email}");

        return back()->with('success', 'User deleted.');
    }

    protected function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users')->ignore($user?->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }
}
