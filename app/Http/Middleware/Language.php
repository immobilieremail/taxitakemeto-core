<?php

namespace App\Http\Middleware;

use Closure,
    Illuminate\Foundation\Application,
    Illuminate\Http\Request,
    Illuminate\Support\Facades\App,
    Illuminate\Support\Facades\Config,
    Illuminate\Support\Facades\Session;

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
        // Access to the first segment of the url
        $segment_1 = $request->segment(1);

        $arr_languages = config('app.languages');

        if (is_null($segment_1) && is_null(Session::get('applocale'))) {
            $fallback_locale = config('app.fallback_locale');
            Session::put('applocale', $fallback_locale);
            Session::put('lang_name', $arr_languages[$fallback_locale]);
        }
        if (isset($arr_languages[$segment_1])) {
            if ($segment_1 != Session::get('applocale')) {
                Session::put('applocale', $segment_1);
                Session::put('lang_name', $arr_languages[$segment_1]);
            }
        }
        if (is_null($segment_1)) {
              return redirect(url('/')."/".Session::get('applocale')."/");
        }
        // Set language for traduction files
        App::setLocale(Session::get('applocale'));

        return $next($request);
    }
}
