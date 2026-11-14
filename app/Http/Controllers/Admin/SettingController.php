<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends AdminController
{
    protected function group(string $group): array
    {
        $def = config("settings.groups.$group");
        abort_unless($def, 404);
        abort_unless(auth()->user()->canAccess($def['permission']), 403);

        return $def;
    }

    public function edit(string $group)
    {
        return view('admin.settings.edit', ['group' => $group, 'def' => $this->group($group)]);
    }

    public function update(Request $request, string $group)
    {
        $def = $this->group($group);
        $rules = [];
        foreach ($def['fields'] as $key => $f) {
            $rules[$key] = match ($f['type']) {
                'email' => ['nullable', 'email', 'max:190'],
                'url' => ['nullable', 'url', 'max:500'],
                'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'number' => ['nullable', 'numeric'],
                'image', 'toggle' => ['nullable'],
                'select' => ['nullable', 'in:'.implode(',', array_keys($f['options'] ?? []))],
                default => ['nullable', 'string', 'max:20000'],
            };
        }
        $request->validate($rules);

        foreach ($def['fields'] as $key => $f) {
            $value = match ($f['type']) {
                'toggle' => $request->boolean($key) ? '1' : '0',
                'image' => $this->image($request, $key, setting($key)),
                default => $request->input($key),
            };
            Setting::updateOrCreate(['key' => $key], ['group' => $group, 'value' => $value]);
        }
        Setting::flush();
        $this->log('update', "Updated {$def['label']}");

        return back()->with('success', $def['label'].' saved successfully.');
    }
}
