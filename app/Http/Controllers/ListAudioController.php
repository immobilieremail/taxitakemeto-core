<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Audio;

class ListAudioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $id = Auth::user()->owner_id;
        $audios = Audio::all()->where('owner_id', $id);

        return view('list-audio', ['audios' => $audios]);
    }
}
