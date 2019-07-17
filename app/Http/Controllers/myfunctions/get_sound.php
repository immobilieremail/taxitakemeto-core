<?php

    use App\Sound,
        App\JoinListSound;

    function getSounds($list_id)
    {
        $audios = array();

        $joins = JoinListSound::all()->where('id_list', $list_id);
        foreach ($joins as $join)
            array_push($audios, Sound::all()->where('id', $join->id_sound));
        return $audios;
    }

