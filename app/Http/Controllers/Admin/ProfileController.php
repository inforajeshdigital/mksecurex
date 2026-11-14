<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends AdminController
{
    public function edit()
    {
        return view('admin.profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);
        $data['avatar'] = $this->image($request, 'avatar', $user->avatar);
        $user->update($data);
        $this->log('update', 'Updated own profile', $user);

        return back()->with('success', 'Profile updated.');
    }

    public function password()
    {
        return view('admin.profile.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);
        $request->user()->update(['password' => Hash::make($request->password)]);
        $request->session()->regenerate();
        $this->log('update', 'Changed password', $request->user());

        return back()->with('success', 'Password changed successfully.');
    }
}
