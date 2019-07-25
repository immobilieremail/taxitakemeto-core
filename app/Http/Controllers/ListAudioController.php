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
    public function index($lang, $suisse_nbr)
    {
        $view_404 = response(view('404'), 404);
        $audios = array();

        $view = View::findByID($suisse_nbr);
        if ($view == NULL)
            return $view_404;

        $list = SoundList::findByID($view->id_list);
        if ($list == NULL)
            return $view_404;

        $audios = getSounds($list->id);
        return response(view('list-audio', ['lists' => $audios, 'lang' => $lang]), 200);
    }
}
