<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\SeoService;

class PageController extends Controller
{
    public function home()
    {
        $page = Page::published()->where('slug', 'home')->with(['seo', 'activeSections'])->firstOrFail();

        return $this->render($page, true);
    }

    public function show(string $slug)
    {
        if ($slug === 'home') {
            return redirect()->route('home', [], 301);
        }
        $page = Page::published()->where('slug', $slug)->with(['seo', 'activeSections'])->firstOrFail();

        return $this->render($page);
    }

    public function render(Page $page, bool $isHome = false, bool $preview = false)
    {
        $sections = $preview ? $page->sections : $page->activeSections;
        $seo = SeoService::build($page, [
            'title' => $isHome ? setting('site_name') : $page->title,
            'description' => $page->excerpt,
            'image' => $page->banner_image,
        ]);

        return view('front.page', [
            'page' => $page,
            'sections' => $sections,
            'seo' => $seo,
            'isHome' => $isHome,
            'preview' => $preview,
            'breadcrumbs' => $isHome ? [] : [['label' => $page->title, 'url' => $page->url]],
        ]);
    }
}
