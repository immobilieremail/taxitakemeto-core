<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index()
    {

        app()->setLocale(session()->get('locale'));
        return view('index');
    }

    public function language(String $locale): Request
    {
        $locale = in_array ($locale, config ('app.locales')) ? $locale : config('app.fallback_locale');
        session (['locale' => $locale]);
        return back();
    }
}
