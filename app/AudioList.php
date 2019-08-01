<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AudioList extends Model
{
    protected $fillable = ['id'];

    public function getAudios()
    {
        $audio_array = array();
        $joinlstaudio = JoinListAudio::where('id_list', $this->id)->get();
        foreach ($joinlstaudio as $join) {
            array_push($audio_array, Audio::find($join->id_audio));
        }
        return $audio_array;
    }
}
