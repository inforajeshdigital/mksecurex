<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ImageService;
use Illuminate\Http\Request;

abstract class AdminController extends Controller
{
    /**
     * Resolve an image input: new upload > picked path from media library > removal > keep current.
     * Form convention: <input type=file name="{field}">, hidden "{field}_path", checkbox "{field}_remove".
     */
    protected function image(Request $request, string $field, ?string $current = null, ?string $alt = null): ?string
    {
        if ($request->hasFile($field)) {
            $request->validate([$field => ImageService::rules()]);

            return app(ImageService::class)->upload($request->file($field), ['alt' => $alt])->path;
        }
        if ($request->boolean($field.'_remove')) {
            return null;
        }
        $picked = trim((string) $request->input($field.'_path'));
        if ($picked !== '' && $picked !== $current) {
            return $this->safePath($picked);
        }

        return $current;
    }

    protected function safePath(string $path): ?string
    {
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        $path = ltrim(str_replace(['..', '\\'], '', $path), '/');

        return $path ?: null;
    }

    /** Clean a repeater array: keep rows that have at least one non-empty value. */
    protected function rows($input, array $keys): array
    {
        $out = [];
        foreach ((array) $input as $row) {
            if (! is_array($row)) {
                continue;
            }
            $clean = [];
            foreach ($keys as $k) {
                $clean[$k] = isset($row[$k]) ? trim((string) $row[$k]) : '';
            }
            if (array_filter($clean, fn ($v) => $v !== '')) {
                $out[] = $clean;
            }
        }

        return $out;
    }

    protected function seoRules(): array
    {
        return [
            'seo.meta_title' => ['nullable', 'string', 'max:255'],
            'seo.meta_description' => ['nullable', 'string', 'max:500'],
            'seo.focus_keyword' => ['nullable', 'string', 'max:255'],
            'seo.secondary_keywords' => ['nullable', 'string', 'max:255'],
            'seo.canonical_url' => ['nullable', 'url', 'max:255'],
            'seo.og_title' => ['nullable', 'string', 'max:255'],
            'seo.og_description' => ['nullable', 'string', 'max:500'],
            'seo.og_url' => ['nullable', 'url', 'max:255'],
            'seo.og_type' => ['nullable', 'in:website,article,product,profile'],
            'seo.twitter_card' => ['nullable', 'in:summary,summary_large_image'],
            'seo.twitter_title' => ['nullable', 'string', 'max:255'],
            'seo.twitter_description' => ['nullable', 'string', 'max:500'],
            'seo.schema_markup' => ['nullable', 'string', 'max:20000'],
        ];
    }

    protected function saveSeoFrom(Request $request, $model): void
    {
        $data = (array) $request->input('seo', []);
        $data['og_image'] = $this->image($request, 'seo_og_image', $model->seo?->og_image);
        $data['twitter_image'] = $this->image($request, 'seo_twitter_image', $model->seo?->twitter_image);
        if (! empty($data['schema_markup'])) {
            json_decode($data['schema_markup']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $data['schema_markup'] = null;
                session()->flash('warning', 'Custom schema was not valid JSON and was not saved.');
            }
        }
        $model->saveSeo($data);
    }

    protected function log(string $action, string $description, $subject = null): void
    {
        ActivityLog::record($action, $description, $subject);
    }
}
