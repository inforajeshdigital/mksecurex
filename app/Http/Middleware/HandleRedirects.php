<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;

class HandleRedirects
{
    public function handle(Request $request, Closure $next)
    {
        if (! in_array($request->method(), ['GET', 'HEAD'], true) || $request->is('admin', 'admin/*')) {
            return $next($request);
        }

        $map = Redirect::activeMap();
        if ($map) {
            $path = Redirect::normalize('/'.$request->path());
            if (isset($map[$path])) {
                $r = $map[$path];
                try {
                    Redirect::whereKey($r['id'])->update(['hits' => \DB::raw('hits + 1'), 'last_hit_at' => now()]);
                } catch (\Throwable $e) {
                }

                return redirect()->to(link_url($r['to']), (int) $r['type'] === 302 ? 302 : 301);
            }
        }

        return $next($request);
    }
}
