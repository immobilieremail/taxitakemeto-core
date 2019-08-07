<?php

namespace App\Http\Controllers;

use App\Audio,
    App\AudioList,
    App\JoinListAudio,
    App\AudioListEditFacet,
    App\AudioListViewFacet,
    Illuminate\Http\Request,
    Illuminate\Support\Facades\Storage;

class ListAudioController extends Controller
{
    public function index($lang, $suisse_nbr)
    {
        $audios = array();
        $view_404 = response(view('404'), 404);

        $view = AudioListViewFacet::find($suisse_nbr);
        if ($view == NULL)
            return $view_404;

        $list = AudioList::find($view->id_list);
        if ($list == NULL)
            return $view_404;

        $joinlstaudio = JoinListAudio::all()->where('id_list', $list->id);
        foreach ($joinlstaudio as $join)
            array_push($audios, Audio::find($join->id_audio));
        return response(view('list-audio', [
            'lists' => $audios,
            'lang' => $lang]), 200);
    }
}
