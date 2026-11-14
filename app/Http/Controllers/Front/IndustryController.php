<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Models\Page;
use App\Services\SeoService;

class IndustryController extends Controller
{
    public function index()
    {
        $page = Page::where('slug', 'industries')->with('seo')->first();

        return view('front.industries.index', [
            'page' => $page,
            'industries' => Industry::published()->ordered()->with(['services' => fn ($q) => $q->published()->ordered()])->get(),
            'seo' => SeoService::build($page, [
                'title' => $page->title ?? 'Industries We Serve',
                'description' => $page->excerpt ?? 'Security, manpower and facility support planned for the way each industry works.',
            ]),
            'breadcrumbs' => [['label' => 'Industries', 'url' => route('industries.index')]],
        ]);
    }

    public function show(string $slug)
    {
        $industry = Industry::published()->where('slug', $slug)
            ->with(['seo', 'services' => fn ($q) => $q->published()->ordered(), 'faqs' => fn ($q) => $q->where('is_active', true)])
            ->firstOrFail();

        return view('front.industries.show', [
            'industry' => $industry,
            'related' => Industry::published()->ordered()->where('id', '!=', $industry->id)->limit(3)->get(),
            'seo' => SeoService::build($industry, [
                'title' => $industry->title,
                'description' => $industry->short_description,
                'image' => $industry->banner_image ?: $industry->image,
            ]),
            'breadcrumbs' => [
                ['label' => 'Industries', 'url' => route('industries.index')],
                ['label' => $industry->title, 'url' => $industry->url],
            ],
        ]);
    }
}
