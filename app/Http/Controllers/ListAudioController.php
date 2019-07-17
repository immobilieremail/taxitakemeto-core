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

        $views = View::all()->where('id_view', $suisse_nbr);
        foreach ($views as $view)
            $first_view = $view;
        echo $first_view . '<br>';

        $lists = SoundList::all()->where('id', $first_view->id_list);
        foreach ($lists as $list)
            $first_list = $list;
        echo $first_list . '<br>';

        $joins = JoinListSound::all()->where('id_list', $first_list->id);
        foreach ($joins as $join)
            array_push($audios, Sound::all()->where('id', $join->id_sound));
        foreach ($audios as $audio)
            echo $audio . '<br>';
        // return view('list-audio', ['lists' => $$audios]);
    }
}
