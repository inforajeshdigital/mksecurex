<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Front;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin panel  (/admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [Admin\AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [Admin\AuthController::class, 'login'])->middleware('throttle:admin-login')->name('login.submit');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', fn () => redirect()->route('admin.dashboard'));
        Route::post('logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Profile & password (every admin)
        Route::get('profile', [Admin\ProfileController::class, 'edit'])->name('profile');
        Route::put('profile', [Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::get('password', [Admin\ProfileController::class, 'password'])->name('password');
        Route::put('password', [Admin\ProfileController::class, 'updatePassword'])->name('password.update');

        // Generic drag & drop ordering
        Route::post('reorder', [Admin\SortController::class, 'reorder'])->name('reorder');

        // Settings groups (permission checked inside per group)
        Route::get('settings/{group}', [Admin\SettingController::class, 'edit'])->name('settings');
        Route::put('settings/{group}', [Admin\SettingController::class, 'update'])->name('settings.update');

        Route::middleware('permission:homepage')->group(function () {
            Route::get('homepage', [Admin\PageController::class, 'homepage'])->name('homepage');
            Route::resource('sliders', Admin\SliderController::class)->except(['show']);
        });

        Route::middleware('permission:pages')->group(function () {
            Route::get('about', [Admin\PageController::class, 'about'])->name('about');
            Route::resource('pages', Admin\PageController::class)->except(['show']);
            Route::get('pages/{page}/preview', [Admin\PageController::class, 'preview'])->name('pages.preview');
        });

        // Sections (page builder) – used by homepage & pages
        Route::get('pages/{page}/sections/create', [Admin\SectionController::class, 'create'])->name('sections.create');
        Route::post('pages/{page}/sections', [Admin\SectionController::class, 'store'])->name('sections.store');
        Route::get('sections/{section}/edit', [Admin\SectionController::class, 'edit'])->name('sections.edit');
        Route::put('sections/{section}', [Admin\SectionController::class, 'update'])->name('sections.update');
        Route::post('sections/{section}/toggle', [Admin\SectionController::class, 'toggle'])->name('sections.toggle');
        Route::post('sections/{section}/duplicate', [Admin\SectionController::class, 'duplicate'])->name('sections.duplicate');
        Route::delete('sections/{section}', [Admin\SectionController::class, 'destroy'])->name('sections.destroy');

        Route::resource('services', Admin\ServiceController::class)->except(['show'])->middleware('permission:services');
        Route::resource('industries', Admin\IndustryController::class)->except(['show'])->middleware('permission:industries');
        Route::resource('testimonials', Admin\TestimonialController::class)->except(['show'])->middleware('permission:testimonials');
        Route::resource('faqs', Admin\FaqController::class)->except(['show'])->middleware('permission:faqs');

        Route::middleware('permission:enquiries')->group(function () {
            Route::get('enquiries/export', [Admin\EnquiryController::class, 'export'])->name('enquiries.export');
            Route::post('enquiries/bulk', [Admin\EnquiryController::class, 'bulk'])->name('enquiries.bulk');
            Route::resource('enquiries', Admin\EnquiryController::class)->only(['index', 'show', 'update', 'destroy']);
        });

        Route::middleware('permission:media')->group(function () {
            Route::get('media/picker', [Admin\MediaController::class, 'picker'])->name('media.picker');
            Route::resource('media', Admin\MediaController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['media' => 'media']);
        });

        Route::middleware('permission:seo')->group(function () {
            Route::get('seo', [Admin\SeoController::class, 'index'])->name('seo.index');
            Route::get('seo/{type}/{id}', [Admin\SeoController::class, 'edit'])->name('seo.edit');
            Route::put('seo/{type}/{id}', [Admin\SeoController::class, 'update'])->name('seo.update');
            Route::get('robots', [Admin\SeoController::class, 'robots'])->name('seo.robots');
            Route::put('robots', [Admin\SeoController::class, 'saveRobots'])->name('seo.robots.save');
            Route::get('sitemap', [Admin\SeoController::class, 'sitemap'])->name('seo.sitemap');
            Route::post('sitemap/refresh', [Admin\SeoController::class, 'refreshSitemap'])->name('seo.sitemap.refresh');
        });

        Route::resource('redirects', Admin\RedirectController::class)->except(['show'])->middleware('permission:redirects');

        Route::middleware('permission:navigation')->group(function () {
            Route::get('menus', [Admin\MenuController::class, 'index'])->name('menus.index');
            Route::post('menus/{menu}/items', [Admin\MenuController::class, 'store'])->name('menus.items.store');
            Route::put('menu-items/{item}', [Admin\MenuController::class, 'update'])->name('menus.items.update');
            Route::delete('menu-items/{item}', [Admin\MenuController::class, 'destroy'])->name('menus.items.destroy');
        });

        Route::middleware('permission:users')->group(function () {
            Route::resource('users', Admin\UserController::class)->except(['show']);
            Route::resource('roles', Admin\RoleController::class)->except(['show']);
            Route::get('activity', [Admin\DashboardController::class, 'activity'])->name('activity');
        });

        Route::middleware('permission:maintenance')->group(function () {
            Route::get('maintenance', [Admin\MaintenanceController::class, 'index'])->name('maintenance');
            Route::post('maintenance/cache', [Admin\MaintenanceController::class, 'clearCache'])->name('maintenance.cache');
            Route::post('maintenance/backup', [Admin\MaintenanceController::class, 'backup'])->name('maintenance.backup');
            Route::post('maintenance/toggle', [Admin\MaintenanceController::class, 'toggle'])->name('maintenance.toggle');
            Route::post('maintenance/migrate', [Admin\MaintenanceController::class, 'migrate'])->name('maintenance.migrate');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
*/
Route::get('robots.txt', [Front\SeoController::class, 'robots'])->name('robots');
Route::get('sitemap.xml', [Front\SeoController::class, 'sitemap'])->name('sitemap');

Route::get('/', [Front\PageController::class, 'home'])->name('home');
Route::get('services', [Front\ServiceController::class, 'index'])->name('services.index');
Route::get('services/{slug}', [Front\ServiceController::class, 'show'])->name('services.show');
Route::get('industries', [Front\IndustryController::class, 'index'])->name('industries.index');
Route::get('industries/{slug}', [Front\IndustryController::class, 'show'])->name('industries.show');
Route::post('enquiry', [Front\EnquiryController::class, 'store'])->middleware('throttle:enquiries')->name('enquiry.store');

// Dynamic CMS pages: /about-us, /contact-us, /privacy-policy, and any page created in admin
Route::get('{slug}', [Front\PageController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('page.show');
