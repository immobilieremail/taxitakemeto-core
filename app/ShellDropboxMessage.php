<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShellDropboxMessage extends Model
{
    protected $fillable = ['id_receiver', 'capability', 'type'];
}
