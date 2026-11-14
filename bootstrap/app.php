<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Runs before routing so redirects work for URLs that no longer exist.
        $middleware->prepend(\App\Http\Middleware\HandleRedirects::class);
        // Runs first of all: mksecurex.com / http://… → https://www.mksecurex.com
        $middleware->prepend(\App\Http\Middleware\ForceCanonicalUrl::class);
        $middleware->web(append: [\App\Http\Middleware\SiteMaintenance::class]);
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
