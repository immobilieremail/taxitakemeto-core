<?php

namespace App;

use App\AudioList;
use App\SwissObject;

class AudioListEditFacet extends SwissObject
{
    protected $fillable = ['id_list'];

    public function audioList()
    {
        return $this->belongsTo(AudioList::class, 'id_list');
    }

    public function getAudios()
    {
        return $this->audioList->getAudios();
    }

    public function getEditableAudios()
    {
        $audios = $this->getAudios();

        $audio_array = collect($audios)->map(function ($audio) {
            return $audio += [
                "update_audio" => "/api/audiolist/$this->swiss_number/audio/" . $audio["audio"]["audio_id"],
                "delete_audio" => "/api/audiolist/$this->swiss_number/audio/" . $audio["audio"]["audio_id"]
            ];
        });
        return $audio_array;
    }

    public function addAudio(String $extension): Audio
    {
        return $this->audioList->audios()->save(new Audio(['extension' => $extension]));
    }
}
