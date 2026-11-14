<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Models\Page;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;

class SeoController extends Controller
{
    public function robots()
    {
        $txt = (string) setting('robots_txt', config('settings.robots_default'));
        $txt = str_replace('{sitemap_url}', route('sitemap'), $txt);

        return response($txt, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function sitemap()
    {
        $xml = Cache::remember('sitemap.xml', 3600, fn () => view('front.sitemap', ['urls' => self::urls()])->render());

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /** All indexable public URLs (published, not noindex, self-canonical). */
    public static function urls(): array
    {
        $urls = [];
        $add = function ($model, string $loc, string $priority) use (&$urls) {
            $seo = $model?->seo;
            if ($seo && ! $seo->robots_index) {
                return;
            }
            if ($seo && $seo->canonical_url && rtrim($seo->canonical_url, '/') !== rtrim($loc, '/')) {
                return; // canonicalised elsewhere
            }
            $urls[] = ['loc' => rtrim($loc, '/') ?: url('/'), 'lastmod' => optional($model?->updated_at)->toAtomString(), 'priority' => $priority];
        };

        $pages = Page::published()->with('seo')->orderBy('sort_order')->get();
        foreach ($pages as $p) {
            $loc = match ($p->slug) {
                'home' => url('/'),
                'services' => route('services.index'),
                'industries' => route('industries.index'),
                default => url($p->slug),
            };
            $add($p, $loc, $p->slug === 'home' ? '1.0' : '0.8');
        }
        if (! $pages->firstWhere('slug', 'services')) {
            $urls[] = ['loc' => route('services.index'), 'lastmod' => null, 'priority' => '0.8'];
        }
        if (! $pages->firstWhere('slug', 'industries')) {
            $urls[] = ['loc' => route('industries.index'), 'lastmod' => null, 'priority' => '0.8'];
        }
        foreach (Service::published()->with('seo')->ordered()->get() as $s) {
            $add($s, $s->url, '0.9');
        }
        foreach (Industry::published()->with('seo')->ordered()->get() as $i) {
            $add($i, $i->url, '0.7');
        }

        return $urls;
    }
}
