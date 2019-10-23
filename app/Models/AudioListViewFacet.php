<?php

namespace App;

use App\SwissObject;
use Illuminate\Database\Eloquent\Model;

class AudioListViewFacet extends SwissObject
{
    protected $fillable = ['id_list'];

    public function audioList()
    {
        return $this->belongsTo(AudioList::class, 'id_list');
    }

    public function shells()
    {
        return $this->morphToMany('App\Shell', 'join_audio_list');
    }

    public function getAudios()
    {
        return $this->audioList->getAudioViews();
    }

    public function getJsonViewFacet()
    {
        return [
            'type' => 'AudioListEdit',
            'contents' => $this->getAudios()
        ];
    }
}
