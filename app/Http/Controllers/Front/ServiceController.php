<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Service;
use App\Services\SeoService;

class ServiceController extends Controller
{
    public function index()
    {
        $page = Page::where('slug', 'services')->with('seo')->first();
        $services = Service::published()->ordered()->get();
        $faqs = Faq::where('is_active', true)->where('category', 'services')->orderBy('sort_order')->get();

        return view('front.services.index', [
            'page' => $page,
            'services' => $services,
            'faqs' => $faqs,
            'seo' => SeoService::build($page, [
                'title' => $page->title ?? 'Our Services',
                'description' => $page->excerpt ?? 'Security, manpower supply, facility management, housekeeping, project, payroll and labour supply services.',
            ]),
            'breadcrumbs' => [['label' => 'Services', 'url' => route('services.index')]],
        ]);
    }

    public function show(string $slug)
    {
        $service = Service::published()->where('slug', $slug)
            ->with(['seo', 'industries' => fn ($q) => $q->published()->ordered(), 'faqs' => fn ($q) => $q->where('is_active', true), 'related' => fn ($q) => $q->published()])
            ->firstOrFail();

        $related = $service->related->isNotEmpty()
            ? $service->related
            : Service::published()->ordered()->where('id', '!=', $service->id)->limit(3)->get();

        return view('front.services.show', [
            'service' => $service,
            'related' => $related,
            'seo' => SeoService::build($service, [
                'title' => $service->title,
                'description' => $service->short_description,
                'image' => $service->banner_image ?: $service->image,
            ]),
            'breadcrumbs' => [
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => $service->title, 'url' => $service->url],
            ],
        ]);
    }
}
