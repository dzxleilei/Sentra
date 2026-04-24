<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireMobileDevice
{
    public function handle(Request $request, Closure $next): Response
    {
        // Desktop access is allowed by default to simplify QA/testing.
        // Set ENABLE_PEMINJAM_MOBILE_ONLY=true to re-enable strict mobile-only mode.
        if (! filter_var(env('ENABLE_PEMINJAM_MOBILE_ONLY', false), FILTER_VALIDATE_BOOL)) {
            return $next($request);
        }

        $agent = strtolower((string) $request->userAgent());

        $isMobile = str_contains($agent, 'android')
            || str_contains($agent, 'iphone')
            || str_contains($agent, 'ipad')
            || str_contains($agent, 'ipod')
            || str_contains($agent, 'mobile')
            || str_contains($agent, 'windows phone');

        if (! $isMobile) {
            return response()->view('peminjam.desktop-blocked');
        }

        return $next($request);
    }
}
