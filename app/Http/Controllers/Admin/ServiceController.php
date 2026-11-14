<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;
use App\Models\Industry;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceController extends AdminController
{
    public function index(Request $request)
    {
        $services = Service::ordered()
            ->when($request->q, fn ($q, $s) => $q->where('title', 'like', "%$s%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->withCount('industries')->get();

        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.form', $this->formData(new Service(['status' => 'published'])));
    }

    public function store(Request $request)
    {
        $service = Service::create($this->validated($request) + ['sort_order' => (int) Service::max('sort_order') + 1]);
        $this->syncRelations($request, $service);
        $this->log('create', "Created service {$service->title}", $service);

        return redirect()->route('admin.services.edit', $service)->with('success', 'Service created.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.form', $this->formData($service->load('seo', 'faqs', 'industries', 'related')));
    }

    public function update(Request $request, Service $service)
    {
        $service->update($this->validated($request, $service));
        $this->syncRelations($request, $service);
        $this->log('update', "Updated service {$service->title}", $service);

        return back()->with('success', 'Service saved.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        $this->log('delete', "Deleted service {$service->title}", $service);

        return redirect()->route('admin.services.index')->with('success', 'Service deleted.');
    }

    protected function formData(Service $service): array
    {
        return [
            'service' => $service,
            'industries' => Industry::ordered()->get(['id', 'title']),
            'allServices' => Service::ordered()->where('id', '!=', $service->id ?? 0)->get(['id', 'title']),
        ];
    }

    protected function validated(Request $request, ?Service $service = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('title'))]);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['required', 'max:150', Rule::unique('services')->ignore($service?->id)],
            'icon' => ['nullable', 'string', 'max:80'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'intro' => ['nullable', 'string', 'max:2000'],
            'description' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:published,draft'],
        ] + $this->seoRules());

        $data['features'] = $this->rows($request->input('features'), ['title', 'text']);
        $data['benefits'] = $this->rows($request->input('benefits'), ['title', 'text']);
        $data['process'] = $this->rows($request->input('process'), ['title', 'text']);
        $data['image'] = $this->image($request, 'image', $service?->image, $data['image_alt'] ?? null);
        $data['banner_image'] = $this->image($request, 'banner_image', $service?->banner_image, $data['image_alt'] ?? null);
        unset($data['seo']);

        return $data;
    }

    protected function syncRelations(Request $request, Service $service): void
    {
        $service->industries()->sync(array_map('intval', (array) $request->input('industry_ids', [])));
        $service->related()->sync(array_values(array_diff(array_map('intval', (array) $request->input('related_ids', [])), [$service->id])));
        $this->syncFaqs($request, $service);
        $this->saveSeoFrom($request, $service);
    }

    protected function syncFaqs(Request $request, $model): void
    {
        $rows = $this->rows($request->input('faqs'), ['question', 'answer']);
        $model->faqs()->delete();
        foreach ($rows as $i => $r) {
            if ($r['question'] && $r['answer']) {
                $model->faqs()->create(['question' => $r['question'], 'answer' => $r['answer'], 'category' => 'item', 'sort_order' => $i + 1]);
            }
        }
    }
}
