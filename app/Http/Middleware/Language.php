<?php

namespace App\Http\Middleware;

use Closure;

class Language
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
        $locale = session()->get('locale');

        if (null === $locale)
        {
            $language = detectBrowserLanguage();
            $language = in_array( $language, ['en', 'fr'] ) ? $language : config('app.fallback_locale') ;
            session()->put('locale', $language);
        }
        
        return $next($request);
    }
}
