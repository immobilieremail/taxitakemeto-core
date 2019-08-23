<?php

namespace App;

use App\SwissObject;

class AudioListEditFacet extends SwissObject
{
    protected $fillable = ['id_list'];

    public static function create(Array $param)
    {
        $obj = new AudioListEditFacet;

        $obj->id_list = $param["id_list"];
        $obj->save();
        return $obj;
    }

    public function getAudios()
    {
        $audiolist = AudioList::find($this->id_list);

        return $audiolist->getAudios();
    }

    public function addAudio(String $extension): Audio
    {
        $audio = Audio::create([
            'extension' => $extension]);
        $joinlstaudio = JoinListAudio::create([
            'id_list' => $this->id_list,
            'id_audio' => $audio->swiss_number]);

        return $audio;
    }
}
