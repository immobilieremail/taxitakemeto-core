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
                    'type' => 'Audio',
                    'audio_id' => $audio->swiss_number,
                    'path_to_file' => str_replace(public_path(), '',
                        Storage::disk('converts')->path($audio->path))
                )
            );
        }
        return $audio_array;
    }
}
