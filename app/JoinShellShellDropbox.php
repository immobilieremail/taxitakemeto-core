<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JoinShellShellDropbox extends Model
{
    public $incrementing = false;
    protected $fillable = ['id_shell', 'id_dropbox'];
}
