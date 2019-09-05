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

    public function getEditableAudios()
    {
        $audios = $this->getAudios();

        foreach ($audios as $audio) {
            array_push($audio,
                array(
                    "delete_audio" => "http://localhost:8000/api/audiolist/$this->swiss_number/audio/" . $audio["audio_id"]
                )
            );
        }
        return $audios;
    }

    public function getViewFacet()
    {
        return AudioListViewFacet::where('id_list', $this->id_list)->first();
    }
}
