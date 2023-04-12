<?php

namespace App\Http\Middleware;

use Closure;

class AjaxSessionExpiredMiddleware
{
    public function handle($request, Closure $next)
    {
        if ($request->ajax() && \Auth::guest()) {
            return response()->json(['message' => 'Session expired'], 403);
        }

        return $next($request);
    }
}
