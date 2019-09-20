<?php

namespace App;

use App\SwissObject;
use Illuminate\Database\Eloquent\Model;

class AudioListViewFacet extends SwissObject
{
    protected $fillable = ['id_list'];

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }


    // public static function create(Array $param)
    // {
    //     $obj = new AudioListViewFacet;

    //     $obj->id_list = $param["id_list"];
    //     $obj->save();
    //     return $obj;
    // }

    public function getAudios()
    {
        $audiolist = AudioList::find($this->id_list);

        return $audiolist->getAudios();
    }
}
