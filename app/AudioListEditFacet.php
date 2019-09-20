<?php

namespace App;

use App\AudioList;
use App\SwissObject;

class AudioListEditFacet extends SwissObject
{
    protected $fillable = ['id_list'];


    public function audioList()
    {
        return $this->belongsTo(AudioList::class, 'id', 'id_list');
    }

    // public static function create(Array $param)
    // {
    //     $obj = new AudioListEditFacet;

    //     $obj->id_list = $param["id_list"];
    //     $obj->save();
    //     return $obj;
    // }

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
                    "audio" => $audio,
                    "update_audio" => "http://localhost:8000/api/audiolist/$this->swiss_number/audio/". $audio["audio_id"],
                    "delete_audio" => "http://localhost:8000/api/audiolist/$this->swiss_number/audio/" . $audio["audio_id"]
                )
            );
        }
        return $audio_array;
    }

    public function addAudio(String $extension): Audio
    {
        $this->audioList->audios()->save(new Audio(['extension' => $extension]));

        $audiolist = AudioList::find($this->id_list);
        $audio = Audio::create(['extension' => $extension]);

        $audiolist->audios()->save($audio);
        return $audio;
    }

    
}
