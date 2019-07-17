<?php

namespace App\Http\Controllers;

use App\Edit,
    App\View,
    App\Sound,
    App\SoundList,
    App\JoinListSound,
    Illuminate\Http\Request,
    Illuminate\Support\Facades\Storage;

require_once __DIR__ . "/myfunctions/rand_nbr.php";

class ListAudioController extends Controller
{
    public function show($suisse_nbr)
    {
        $audios = array();

        $view = View::getFirstView($suisse_nbr);
        if ($view == NULL)
            return view('404');

        $list = SoundList::getFirstSoundList($view->id_list);
        if ($list == NULL)
            return view('404');

        $joins = JoinListSound::all()->where('id_list', $list->id);
        foreach ($joins as $join)
            array_push($audios, Sound::all()->where('id', $join->id_sound));
        return view('list-audio', ['lists' => $audios]);
    }
}
