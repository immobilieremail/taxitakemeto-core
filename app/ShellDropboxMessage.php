<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShellDropboxMessage extends Model
{
    protected $fillable = ['id', 'id_receiver', 'capability', 'type'];
}
