<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends AdminController
{
    public function index()
    {
        return redirect()->route('admin.users.index');
    }

    public function create()
    {
        return view('admin.users.role', ['role' => new Role(['permissions' => ['dashboard']])]);
    }

    public function store(Request $request)
    {
        $role = Role::create($this->validated($request));
        $this->log('create', "Created role {$role->name}", $role);

        return redirect()->route('admin.users.index')->with('success', 'Role created.');
    }

    public function edit(Role $role)
    {
        return view('admin.users.role', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $this->validated($request, $role);
        if ($role->slug === 'super-admin') {
            $data['permissions'] = ['*'];
            $data['slug'] = 'super-admin';
        }
        $role->update($data);
        $this->log('update', "Updated role {$role->name}", $role);

        return redirect()->route('admin.users.index')->with('success', 'Role saved.');
    }

    public function destroy(Role $role)
    {
        if ($role->slug === 'super-admin' || $role->users()->exists()) {
            return back()->with('error', 'This role cannot be deleted (system role or has users).');
        }
        $role->delete();

        return back()->with('success', 'Role deleted.');
    }

    protected function validated(Request $request, ?Role $role = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('name'))]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'slug' => ['required', Rule::unique('roles')->ignore($role?->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['in:'.implode(',', array_keys(config('admin.permissions')))],
        ]);
        $data['permissions'] = array_values(array_unique(array_merge(['dashboard'], $data['permissions'] ?? [])));

        return $data;
    }
}
