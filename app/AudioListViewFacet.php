<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AudioListViewFacet extends Model
{
    protected $fillable = ['id_list'];

    public function __construct()
    {
        parent::__construct();
    }

    public static function create(Array $param)
    {
        $obj = new AudioListViewFacet;

        $obj->id_list = $param["id_list"];
        $obj->save();
        return $obj;
    }
}
