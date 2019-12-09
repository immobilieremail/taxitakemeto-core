<?php

namespace App\Models;

class ShellUserFacet extends Facet
{
    public function target()
    {
        return $this->belongsTo(Shell::class);
    }
}
