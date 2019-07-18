<?php

namespace App\Http\Controllers;

use App\Edit,
    App\View,
    App\Sound,
    App\SoundList,
    App\JoinListSound,
    Illuminate\Http\Request,
    Illuminate\Support\Facades\Storage;

require_once __DIR__ . "/myfunctions/get_sound.php";

class ListAudioController extends Controller
{
    public function index($suisse_nbr)
    {
        $audios = array();

        $view = View::getFirstView($suisse_nbr);
        if ($view == NULL)
            return view('404');

        $list = SoundList::getFirstSoundList($view->id_list);
        if ($list == NULL)
            return view('404');

        $audios = getSounds($list->id);
        return view('list-audio', ['lists' => $audios]);
    }
}
