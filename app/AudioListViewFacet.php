<?php

namespace App;

use App\SwissObject;

class AudioListViewFacet extends SwissObject
{
    protected $fillable = ['id_list'];

    public static function create(Array $param)
    {
        $obj = new AudioListViewFacet;

        $obj->id_list = $param["id_list"];
        $obj->save();
        return $obj;
    }

    public function shells()
    {
        return $this->morphToMany('App\Shell', 'join_audio_list');
    }

    public function getAudios()
    {
        $audiolist = AudioList::find($this->id_list);

        return $audiolist->getAudioViews();
    }

    public function getJsonViewFacet()
    {
        return [
            "type" => 'AudioListView',
            "contents" => $this->getAudios()
        ];
    }
}
