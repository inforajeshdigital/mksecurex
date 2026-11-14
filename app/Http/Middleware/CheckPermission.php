<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $module)
    {
        if (! $request->user()?->canAccess($module)) {
            abort(403, 'You do not have permission to access this section.');
        }

        return $next($request);
    }
}
