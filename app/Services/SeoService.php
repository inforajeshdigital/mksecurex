<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Builds one consistent set of meta tags per request from DB values with sensible fallbacks.
 */
class SeoService
{
    public static function build($model = null, array $defaults = []): array
    {
        $seo = $model?->seo;
        $suffix = (string) setting('seo_title_suffix');
        $baseTitle = $defaults['title'] ?? setting('site_name');

        $title = $seo?->meta_title ?: ($baseTitle.(Str::contains($baseTitle, trim($suffix, ' |')) ? '' : $suffix));
        $description = $seo?->meta_description ?: ($defaults['description'] ?? setting('seo_default_description'));
        $description = Str::limit(trim(strip_tags((string) $description)), 300);

        $canonical = $seo?->canonical_url ?: self::canonical();
        $image = img_url($seo?->og_image ?: ($defaults['image'] ?? setting('seo_default_og_image')));

        $robots = (($seo?->robots_index ?? true) ? 'index' : 'noindex').', '.(($seo?->robots_follow ?? true) ? 'follow' : 'nofollow');
        if (setting_on('maintenance_mode')) {
            $robots = 'noindex, nofollow';
        }

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => trim(implode(', ', array_filter([$seo?->focus_keyword, $seo?->secondary_keywords])), ', '),
            'canonical' => $canonical,
            'robots' => $robots,
            'og' => [
                'title' => $seo?->og_title ?: $title,
                'description' => $seo?->og_description ?: $description,
                'image' => $image,
                'url' => $seo?->og_url ?: $canonical,
                'type' => $seo?->og_type ?: ($defaults['og_type'] ?? 'website'),
            ],
            'twitter' => [
                'card' => $seo?->twitter_card ?: 'summary_large_image',
                'title' => $seo?->twitter_title ?: ($seo?->og_title ?: $title),
                'description' => $seo?->twitter_description ?: ($seo?->og_description ?: $description),
                'image' => img_url($seo?->twitter_image) ?: $image,
            ],
            'schema' => $seo?->schema_markup,
        ];
    }

    /** Self-referencing canonical: current URL without query string, consistent trailing slash rule. */
    public static function canonical(): string
    {
        $url = url()->current();
        if (setting_on('seo_trailing_slash')) {
            $url = rtrim($url, '/') ?: url('/');
        } elseif (! Str::endsWith($url, '/')) {
            $url .= '/';
        }

        return $url;
    }
}
