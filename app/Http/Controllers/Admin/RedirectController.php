<?php

namespace App\Http\Controllers\Admin;

use App\Models\Redirect;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RedirectController extends AdminController
{
    public function index(Request $request)
    {
        $redirects = Redirect::when($request->q, fn ($q, $s) => $q->where('from_path', 'like', "%$s%")->orWhere('to_url', 'like', "%$s%"))
            ->latest()->paginate(25)->withQueryString();

        return view('admin.redirects.index', compact('redirects'));
    }

    public function create()
    {
        return view('admin.redirects.form', ['redirect' => new Redirect(['type' => 301, 'is_active' => true])]);
    }

    public function store(Request $request)
    {
        $r = Redirect::create($this->validated($request));
        $this->log('create', "Created redirect {$r->from_path} → {$r->to_url}", $r);

        return redirect()->route('admin.redirects.index')->with('success', 'Redirect created.');
    }

    public function edit(Redirect $redirect)
    {
        return view('admin.redirects.form', compact('redirect'));
    }

    public function update(Request $request, Redirect $redirect)
    {
        $redirect->update($this->validated($request, $redirect));
        $this->log('update', "Updated redirect {$redirect->from_path}", $redirect);

        return redirect()->route('admin.redirects.index')->with('success', 'Redirect saved.');
    }

    public function destroy(Redirect $redirect)
    {
        $redirect->delete();
        $this->log('delete', "Deleted redirect {$redirect->from_path}");

        return back()->with('success', 'Redirect deleted.');
    }

    protected function validated(Request $request, ?Redirect $redirect = null): array
    {
        $request->merge(['from_path' => Redirect::normalize((string) $request->input('from_path'))]);
        $data = $request->validate([
            'from_path' => ['required', 'string', 'max:255', 'not_in:/', Rule::unique('redirects')->ignore($redirect?->id)],
            'to_url' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:301,302'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        if (str_starts_with($data['from_path'], '/admin')) {
            throw ValidationException::withMessages(['from_path' => 'Admin URLs cannot be redirected.']);
        }

        $to = $data['to_url'];
        $isAbsolute = preg_match('#^https?://#i', $to);
        if (! $isAbsolute && ! str_starts_with($to, '/')) {
            $data['to_url'] = $to = '/'.ltrim($to, '/');
        }
        if ($isAbsolute && ! filter_var($to, FILTER_VALIDATE_URL)) {
            throw ValidationException::withMessages(['to_url' => 'Destination must be a valid URL or a path starting with /.']);
        }

        // Internal destination path (also for absolute URLs on this host)
        $host = parse_url(config('app.url'), PHP_URL_HOST);
        $toPath = (! $isAbsolute || parse_url($to, PHP_URL_HOST) === $host) ? Redirect::normalize($to) : null;

        if ($toPath === $data['from_path']) {
            throw ValidationException::withMessages(['to_url' => 'Destination cannot be the same as the source (redirect loop).']);
        }
        if ($toPath && Redirect::where('from_path', $toPath)->where('id', '!=', $redirect?->id)->where('is_active', true)->exists()) {
            throw ValidationException::withMessages(['to_url' => 'The destination is itself redirected. Point directly to the final URL to avoid a redirect chain.']);
        }
        if (Redirect::where('to_url', 'like', '%'.$data['from_path'])->where('id', '!=', $redirect?->id)->where('is_active', true)->exists()) {
            session()->flash('warning', 'Another redirect points to this source URL. Consider updating it to the final destination to avoid a chain.');
        }

        return $data;
    }
}
