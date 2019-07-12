<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadAudioController extends Controller
{
    public function index()
    {
        return view('upload-audio');
    }
}
