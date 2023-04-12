<?php

namespace App\Http\Middleware;

use Closure;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {  
        // available language in template array
        $availLocale=['en'=>'en', 'fr'=>'fr','de'=>'de','pt'=>'pt','es'=>'es'];

        // Locale is enabled and allowed to be change
        if(session()->has('locale') && property_exists($availLocale, session()->get('locale'))){
             // Set the Laravel locale
            app()->setLocale(session()->get('locale'));
        }
        return $next($request);
    }
}
