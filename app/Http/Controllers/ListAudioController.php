<?php

namespace App\Http\Controllers;

use App\Edit;
use App\View;
use App\Sound;
use App\SoundList;
use App\JoinListSound;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

require_once __DIR__ . "/myfunctions/rand_nbr.php";

class ListAudioController extends Controller
{
    public function show($suisse_nbr)
    {
        $audios = array();
        $first_view = NULL;
        $first_list = NULL;

        $views = View::all()->where('id_view', $suisse_nbr);
        foreach ($views as $view)
            $first_view = $view;
        if ($first_view == NULL)
            return view('404');

        $lists = SoundList::all()->where('id', $first_view->id_list);
        foreach ($lists as $list)
            $first_list = $list;
        if ($first_list == NULL)
            return view('404');

        $joins = JoinListSound::all()->where('id_list', $first_list->id);
        foreach ($joins as $join)
            array_push($audios, Sound::all()->where('id', $join->id_sound));
        return view('list-audio', ['lists' => $audios]);
    }
}
