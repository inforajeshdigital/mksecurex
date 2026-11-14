<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Front\PageController as FrontPage;
use App\Models\Page;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageController extends AdminController
{
    /** Slugs owned by fixed routes – cannot be used for CMS pages. */
    protected const RESERVED = ['admin', 'services', 'industries', 'enquiry', 'robots', 'sitemap', 'storage', 'uploads', 'assets', 'images', 'up'];

    public function index()
    {
        return view('admin.pages.index', ['pages' => Page::withCount('sections')->orderBy('sort_order')->get()]);
    }

    public function homepage()
    {
        $page = Page::where('slug', 'home')->firstOrFail();

        return view('admin.pages.builder', [
            'page' => $page->load('sections'),
            'sliders' => Slider::orderBy('sort_order')->get(),
            'isHome' => true,
        ]);
    }

    public function about()
    {
        $page = Page::where('slug', 'about-us')->first();

        return $page ? redirect()->route('admin.pages.edit', $page) : redirect()->route('admin.pages.index')->with('warning', 'About Us page not found.');
    }

    public function create()
    {
        return view('admin.pages.form', ['page' => new Page(['status' => 'draft'])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $page = Page::create($data);
        $this->saveSeoFrom($request, $page);
        $this->log('create', "Created page {$page->title}", $page);

        return redirect()->route('admin.pages.edit', $page)->with('success', 'Page created. Now add sections below.');
    }

    public function edit(Page $page)
    {
        if ($page->slug === 'home') {
            return redirect()->route('admin.homepage');
        }

        return view('admin.pages.form', ['page' => $page->load('sections', 'seo')]);
    }

    public function update(Request $request, Page $page)
    {
        $data = $this->validated($request, $page);
        $page->update($data);
        $this->saveSeoFrom($request, $page);
        $this->log('update', "Updated page {$page->title}", $page);

        return back()->with('success', 'Page saved.');
    }

    public function destroy(Page $page)
    {
        if ($page->is_system) {
            return back()->with('error', 'System pages cannot be deleted. You can set them to draft instead.');
        }
        $page->delete();
        $this->log('delete', "Deleted page {$page->title}", $page);

        return redirect()->route('admin.pages.index')->with('success', 'Page deleted.');
    }

    public function preview(Page $page)
    {
        $controller = app(FrontPage::class);

        return $controller->render($page->load(['seo', 'sections']), $page->slug === 'home', true);
    }

    protected function validated(Request $request, ?Page $page = null): array
    {
        $request->merge(['slug' => $page?->is_system ? $page->slug : Str::slug($request->input('slug') ?: $request->input('title'))]);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['required', 'string', 'max:150', Rule::unique('pages')->ignore($page?->id), $page?->is_system ? 'present' : Rule::notIn(self::RESERVED)],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'banner_alt' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:published,draft'],
            'published_on' => ['nullable', 'date'],
        ] + $this->seoRules());

        if ($page?->is_system) {
            $data['slug'] = $page->slug; // keep system URLs stable
        }
        $data['banner_image'] = $this->image($request, 'banner_image', $page?->banner_image, $data['banner_alt'] ?? null);
        unset($data['seo']);

        return $data;
    }
}
