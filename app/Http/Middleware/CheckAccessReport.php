<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Closure;

class CheckAccessReport
{
    public function handle($request, Closure $next)
    {
        $settings = json_decode(DB::table('wh_organization')->first()->settings);

        if(!property_exists($settings, 'only_admin_access_reports') || $settings->only_admin_access_reports == false)
        {
            return $next($request);
        }

        if($settings->only_admin_access_reports == true && Auth::user()->isadmin == true )
        {
            return $next($request);
        }

        return redirect('not-authorized');
    }
}
