<?php

namespace App;

use App\Audio;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Storage;

class AudioList extends Model
{
    protected $fillable = ['id'];

    public function audioEdits()
    {
        return $this->morphedByMany('App\AudioEditFacet', 'join_audio');
    }

    public function audioViews()
    {
        return $this->morphedByMany('App\AudioViewFacet', 'join_audio');
    }

    public function getAudioViews()
    {
        $audios = $this->audioViews;
        $audio_array = array();

        foreach ($audios as $audio) {
            array_push($audio_array,
                array(
                    'type' => 'ocap',
                    'ocapType' => 'AudioViewFacet',
                    'url' => "/api/audio/$audio->swiss_number"
                )
            );
        }
        return $audio_array;
    }

    public function getAudioEdits()
    {
        $audios = $this->audioEdits;
        $audio_array = array();

        foreach ($audios as $audio) {
            array_push($audio_array,
                array(
                    'type' => 'ocap',
                    'ocapType' => 'AudioEditFacet',
                    'url' => "/api/audio/$audio->swiss_number"
                )
            );
        }
        return $audio_array;
    }
}
