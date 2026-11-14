<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Front\SeoController as FrontSeo;
use App\Models\Industry;
use App\Models\Page;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SeoController extends AdminController
{
    protected const TYPES = ['page' => Page::class, 'service' => Service::class, 'industry' => Industry::class];

    protected function find(string $type, int $id)
    {
        abort_unless(isset(self::TYPES[$type]), 404);

        return self::TYPES[$type]::with('seo')->findOrFail($id);
    }

    public function index()
    {
        $items = collect();
        foreach (self::TYPES as $type => $class) {
            $class::with('seo')->orderBy('sort_order')->get()->each(function ($m) use ($items, $type) {
                $items->push(['type' => $type, 'model' => $m, 'url' => $type === 'page' ? match ($m->slug) {
                    'services' => route('services.index'), 'industries' => route('industries.index'), default => $m->url,
                } : $m->url]);
            });
        }

        return view('admin.seo.index', compact('items'));
    }

    public function edit(string $type, int $id)
    {
        $model = $this->find($type, $id);

        return view('admin.seo.edit', ['type' => $type, 'model' => $model, 'url' => $type === 'page' ? $model->url : $model->url]);
    }

    public function update(Request $request, string $type, int $id)
    {
        $model = $this->find($type, $id);
        $request->validate($this->seoRules());
        $this->saveSeoFrom($request, $model);
        Cache::forget('sitemap.xml');
        $this->log('update', "Updated SEO for {$model->title}", $model);

        return back()->with('success', 'SEO settings saved.');
    }

    public function robots()
    {
        return view('admin.seo.robots', ['content' => setting('robots_txt', config('settings.robots_default'))]);
    }

    public function saveRobots(Request $request)
    {
        $content = $request->boolean('restore') ? config('settings.robots_default') : $request->validate(['content' => ['required', 'string', 'max:10000']])['content'];
        Setting::put('robots_txt', str_replace("\r\n", "\n", $content), 'seo');
        $this->log('update', 'Updated robots.txt');

        return back()->with('success', $request->boolean('restore') ? 'Default robots.txt restored.' : 'robots.txt saved.');
    }

    public function sitemap()
    {
        return view('admin.seo.sitemap', ['urls' => FrontSeo::urls()]);
    }

    public function refreshSitemap()
    {
        Cache::forget('sitemap.xml');

        return back()->with('success', 'Sitemap regenerated.');
    }
}
