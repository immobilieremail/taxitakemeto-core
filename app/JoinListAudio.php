<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JoinListAudio extends Model
{
    public $incrementing = false;
    protected $fillable = ['id_list', 'id_audio'];
}
