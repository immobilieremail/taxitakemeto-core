<?php

namespace App\Models;

use App\SwissObject;

class MediaEdit extends SwissObject
{
    public function target()
    {
        return $this->morphTo();
    }
}
