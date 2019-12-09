<?php

namespace App\Models;

class ShellUserFacet extends Facet
{
    public function __construct()
    {
        parent::__construct();

        $this->type = 'App\Models\ShellUserFacet';
    }

    public function target()
    {
        return $this->belongsTo(Shell::class);
    }
}
