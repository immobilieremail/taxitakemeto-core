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

        return $audiolist->getAudioViews();
    }

    public function getViewFacet()
    {
        return AudioListViewFacet::where('id_list', $this->id_list)->first();
    }
}
