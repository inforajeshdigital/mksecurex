<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SiteMaintenance
{
    public function handle(Request $request, Closure $next)
    {
        if (setting_on('maintenance_mode') && ! $request->is('admin', 'admin/*', 'robots.txt', 'sitemap.xml') && ! $request->user()) {
            return response()->view('front.maintenance', [], 503)->header('Retry-After', '3600');
        }

        return $next($request);
    }
}
