<?php

namespace App\Models;

use App\SwissObject;

class MediaView extends SwissObject
{
    public function target()
    {
        return $this->morphTo();
    }
}
