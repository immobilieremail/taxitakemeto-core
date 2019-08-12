<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JoinDropboxToMsg extends Model
{
    public $incrementing = false;
    protected $fillable = ['id_dropbox', 'id_msg'];
}
