<?php

    use App\Audio,
        App\JoinListAudio;

    function getSounds($list_id)
    {
        $audios = array();

        $joins = JoinListAudio::all()->where('id_list', $list_id);
        foreach ($joins as $join)
            array_push($audios, Audio::all()->where('id', $join->id_audio));
        return $audios;
    }

