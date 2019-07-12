<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ListAudioController extends Controller
{
    public function index()
    {
        return view('list-audio');
    }
}
