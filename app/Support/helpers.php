<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        $value = Setting::get($key);
        if ($value === null) {
            $value = $default ?? config("settings.defaults.$key");
        }

        return $value;
    }
}

if (! function_exists('setting_on')) {
    function setting_on(string $key): bool
    {
        return in_array((string) setting($key, '0'), ['1', 'true', 'on', 'yes'], true);
    }
}

if (! function_exists('img_url')) {
    /** Resolve a stored image path (relative to /public) or absolute URL. */
    function img_url(?string $path, ?string $fallback = null): ?string
    {
        if (! $path) {
            return $fallback;
        }
        if (preg_match('#^(https?:)?//#', $path) || str_starts_with($path, 'data:')) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }
}

if (! function_exists('link_url')) {
    function link_url(?string $url): string
    {
        $u = trim((string) $url);
        if ($u === '') {
            return '#';
        }
        if (preg_match('#^(https?:|mailto:|tel:|\#|//)#i', $u)) {
            return $u;
        }
        if (preg_match('#^javascript:#i', $u)) {
            return '#';
        }

        return url($u);
    }
}

if (! function_exists('wa_link')) {
    function wa_link(?string $message = null): ?string
    {
        $num = preg_replace('/\D+/', '', (string) setting('whatsapp_number'));
        if (! $num) {
            return null;
        }
        $msg = $message ?? setting('whatsapp_message');

        return 'https://wa.me/'.$num.($msg ? '?text='.rawurlencode($msg) : '');
    }
}

if (! function_exists('tel_link')) {
    function tel_link(?string $phone): string
    {
        return 'tel:'.preg_replace('/[^\d+]/', '', (string) $phone);
    }
}

if (! function_exists('lines')) {
    /** Turn a textarea (one item per line) into an array. */
    function lines(?string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $text))));
    }
}

if (! function_exists('admin_can')) {
    function admin_can(string $module): bool
    {
        return auth()->check() && auth()->user()->canAccess($module);
    }
}
