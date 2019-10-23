<?php

namespace App;

use App\SwissObject;

class ShellDropboxFacet extends SwissObject
{
    protected $fillable = ['id_shell'];

    public function shell()
    {
        return $this->belongsTo(Shell::class, 'id_shell');
    }
}
