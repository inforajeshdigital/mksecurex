<?php

namespace App\Http\Controllers\Admin;

use App\Models\Industry;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class IndustryController extends AdminController
{
    public function index(Request $request)
    {
        $industries = Industry::ordered()
            ->when($request->q, fn ($q, $s) => $q->where('title', 'like', "%$s%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->withCount('services')->get();

        return view('admin.industries.index', compact('industries'));
    }

    public function create()
    {
        return view('admin.industries.form', ['industry' => new Industry(['status' => 'published']), 'services' => Service::ordered()->get(['id', 'title'])]);
    }

    public function store(Request $request)
    {
        $industry = Industry::create($this->validated($request) + ['sort_order' => (int) Industry::max('sort_order') + 1]);
        $this->syncRelations($request, $industry);
        $this->log('create', "Created industry {$industry->title}", $industry);

        return redirect()->route('admin.industries.edit', $industry)->with('success', 'Industry created.');
    }

    public function edit(Industry $industry)
    {
        return view('admin.industries.form', ['industry' => $industry->load('seo', 'faqs', 'services'), 'services' => Service::ordered()->get(['id', 'title'])]);
    }

    public function update(Request $request, Industry $industry)
    {
        $industry->update($this->validated($request, $industry));
        $this->syncRelations($request, $industry);
        $this->log('update', "Updated industry {$industry->title}", $industry);

        return back()->with('success', 'Industry saved.');
    }

    public function destroy(Industry $industry)
    {
        $industry->delete();
        $this->log('delete', "Deleted industry {$industry->title}", $industry);

        return redirect()->route('admin.industries.index')->with('success', 'Industry deleted.');
    }

    protected function validated(Request $request, ?Industry $industry = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('title'))]);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['required', 'max:150', Rule::unique('industries')->ignore($industry?->id)],
            'icon' => ['nullable', 'string', 'max:80'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'intro' => ['nullable', 'string', 'max:2000'],
            'description' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:published,draft'],
        ] + $this->seoRules());

        $data['workforce_requirements'] = $this->rows($request->input('workforce_requirements'), ['title', 'text']);
        $data['challenges'] = $this->rows($request->input('challenges'), ['title', 'text']);
        $data['solutions'] = $this->rows($request->input('solutions'), ['title', 'text']);
        $data['benefits'] = $this->rows($request->input('benefits'), ['title', 'text']);
        $data['show_banner'] = $request->boolean('show_banner');
        $data['show_image'] = $request->boolean('show_image');
        $data['image'] = $this->image($request, 'image', $industry?->image, $data['image_alt'] ?? null);
        $data['banner_image'] = $this->image($request, 'banner_image', $industry?->banner_image, $data['image_alt'] ?? null);
        unset($data['seo']);

        return $data;
    }

    protected function syncRelations(Request $request, Industry $industry): void
    {
        $industry->services()->sync(array_map('intval', (array) $request->input('service_ids', [])));
        $rows = $this->rows($request->input('faqs'), ['question', 'answer']);
        $industry->faqs()->delete();
        foreach ($rows as $i => $r) {
            if ($r['question'] && $r['answer']) {
                $industry->faqs()->create(['question' => $r['question'], 'answer' => $r['answer'], 'category' => 'item', 'sort_order' => $i + 1]);
            }
        }
        $this->saveSeoFrom($request, $industry);
    }
}
