<?php

namespace App;

use App\Audio;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Storage;

class AudioList extends Model
{
    protected $fillable = ['id'];

    public function audios()
    {
        return $this->hasMany('App\Audio');
    }

    public function getAudios()
    {
        $audios = $this->audios;
        $audio_array = array();

        foreach ($audios as $audio) {
            array_push($audio_array,
                array(
                    'type' => 'ocap',
                    'ocapType' => 'Audio',
                    'url' => "http://localhost:8000/api/audio/$audio->swiss_number"
                )
            );
        }
        return $audio_array;
    }
}
