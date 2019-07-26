<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AudioList extends Model
{
    protected $fillable = ['id'];

    public static function findByID($list_id)
    {
        $first_AudioList = AudioList::where('id', $list_id)->first();

        return $first_AudioList;
    }
}
