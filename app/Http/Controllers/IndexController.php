<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class IndexController extends Controller
{
    public function index(): View
    {
        return view('index');
    }

    public function language(String $locale): RedirectResponse
    {
        $locale = in_array ($locale, config ('app.locales')) ? $locale : config('app.fallback_locale');
        session(['locale' => $locale]);
        return back();
    }
}
