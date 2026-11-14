<?php

namespace App\Providers;

use App\Models\Enquiry;
use App\Models\Industry;
use App\Models\NavigationMenu;
use App\Models\Service;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        if ($this->app->environment('production') && str_starts_with((string) config('app.url'), 'https://')) {
            \URL::forceScheme('https');
        }

        RateLimiter::for('enquiries', fn (Request $r) => Limit::perMinute(5)->by($r->ip()));
        RateLimiter::for('admin-login', fn (Request $r) => Limit::perMinute(6)->by(strtolower((string) $r->input('email')).'|'.$r->ip()));

        // Global data for the public layout (cached, flushed on change).
        View::composer(['layouts.app', 'front.*', 'errors::*', 'errors.*'], function ($view) {
            static $shared = null;
            if ($shared === null) {
                try {
                    $shared = [
                        'navServices' => Cache::rememberForever('nav.services', fn () => Service::published()->ordered()->get(['id', 'title', 'slug', 'icon'])),
                        'navIndustries' => Cache::rememberForever('nav.industries', fn () => Industry::published()->ordered()->get(['id', 'title', 'slug', 'icon'])),
                        'headerMenu' => NavigationMenu::forLocation('header'),
                        'footerQuick' => NavigationMenu::forLocation('footer_quick'),
                        'footerLegal' => NavigationMenu::forLocation('footer_legal'),
                    ];
                } catch (\Throwable $e) {
                    $shared = ['navServices' => collect(), 'navIndustries' => collect(), 'headerMenu' => collect(), 'footerQuick' => collect(), 'footerLegal' => collect()];
                }
            }
            $view->with($shared);
        });

        View::composer('layouts.admin', function ($view) {
            try {
                $view->with('newEnquiries', Enquiry::where('status', 'new')->where('is_spam', false)->count());
            } catch (\Throwable $e) {
                $view->with('newEnquiries', 0);
            }
            // After a Git deploy, tell the admin when database updates are waiting.
            try {
                $migrator = app('migrator');
                $files = array_keys($migrator->getMigrationFiles(database_path('migrations')));
                $view->with('pendingMigrations', count(array_diff($files, $migrator->getRepository()->getRan())));
            } catch (\Throwable $e) {
                $view->with('pendingMigrations', 0);
            }
        });

        foreach ([Service::class, Industry::class] as $model) {
            $model::saved(fn () => Cache::forget($model === Service::class ? 'nav.services' : 'nav.industries'));
            $model::deleted(fn () => Cache::forget($model === Service::class ? 'nav.services' : 'nav.industries'));
            $model::restored(fn () => Cache::forget($model === Service::class ? 'nav.services' : 'nav.industries'));
        }
    }
}
