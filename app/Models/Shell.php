<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shell extends Model
{
    public function userFacet()
    {
        return $this->hasOne(ShellUserFacet::class, 'target_id')
                    ->where('type', 'App\Models\ShellUserFacet');
    }
}
