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
        $audio_array = array();

        foreach ($audios as $audio) {
            array_push($audio_array,
                array(
                    "audios" => $audio,
                    "update_audio" => "http://localhost:8000/api/audiolist/$this->swiss_number/audio/$audio->swiss_number",
                    "delete_audio" => "http://localhost:8000/api/audiolist/$this->swiss_number/audio/$audio->swiss_number"
                )
            );
        }
        return $audio_array;
    }

    public function addAudio(String $extension): Audio
    {
        $audiolist = AudioList::find($this->id_list);
        $audio = Audio::create(['extension' => $extension]);

        $audiolist->audios()->save($audio);
        return $audio;
    }

    public function getViewFacet()
    {
        return AudioListViewFacet::where('id_list', $this->id_list)->first();
    }
}
