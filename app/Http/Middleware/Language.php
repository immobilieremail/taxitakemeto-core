<?php

namespace App\Http\Middleware;

<<<<<<< HEAD
use Closure;
=======
use Closure,
    Illuminate\Foundation\Application,
    Illuminate\Http\Request,
    Illuminate\Support\Facades\App,
    Illuminate\Support\Facades\Config,
    Illuminate\Support\Facades\Session;
>>>>>>> c84982b970c5d4b5b8285329b8d125ce4f645e27

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
<<<<<<< HEAD
        if (!session()->has('locale')) {
            session(['locale' => $request->getPreferredLanguage(config('app.locales'))]);
        }

        $locale = session('locale');
        app()->setLocale($locale);

        setlocale(LC_TIME, app()->environment('local') ? $locale : config('locale.languages')[$locale][1]);
=======
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
>>>>>>> c84982b970c5d4b5b8285329b8d125ce4f645e27

        return $next($request);
    }
}
