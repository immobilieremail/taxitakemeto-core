<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = [
        'sender', 'recipient'
    ];

    public function recipient()
    {
        return $this->belongsTo(Facet::class, 'recipient');
    }

    public function sender()
    {
        return $this->belongsTo(Facet::class, 'sender');
    }
}
