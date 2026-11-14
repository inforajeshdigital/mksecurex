<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Sends every visitor to one address, e.g. https://www.mksecurex.com.
 *
 *  - mksecurex.com (http or https)  → https://www.mksecurex.com/same/path?query
 *  - http://www.mksecurex.com       → https://www.mksecurex.com/same/path?query
 *
 * Only requests for the site's own domain are touched, so localhost / staging
 * keep working. Set CANONICAL_URL= (empty) in .env to switch this off.
 */
class ForceCanonicalUrl
{
    public function handle(Request $request, Closure $next)
    {
        $canonical = (string) config('app.canonical_url');
        $target = strtolower((string) parse_url($canonical, PHP_URL_HOST));
        if ($target === '') {
            return $next($request);
        }

        $bare = preg_replace('/^www\./', '', $target);
        $host = strtolower($request->getHost());
        if ($host !== $target && $host !== $bare && $host !== 'www.'.$bare) {
            return $next($request); // some other host (localhost, preview domain…)
        }

        $wrongHost = $host !== $target;
        // Hostinger's CDN already turns http into https before it reaches us, and it may
        // talk to the server over plain http. So a request is only treated as insecure when
        // the proxy explicitly says it was http – this can never cause a redirect loop.
        $proto = strtolower(trim(explode(',', (string) $request->header('X-Forwarded-Proto'))[0]));
        $insecure = $proto === 'http';

        if ($wrongHost || $insecure) {
            $status = in_array($request->method(), ['GET', 'HEAD'], true) ? 301 : 308;

            return redirect()->away('https://'.$target.$request->getRequestUri(), $status);
        }

        return $next($request);
    }
}
