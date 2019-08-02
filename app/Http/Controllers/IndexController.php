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
}
