<?php

namespace App\Models;

use Illuminate\Http\Request;

class ShellDropboxFacet extends Facet
{
    /**
     * Inverse relation of DropboxFacet for specific Shell
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(Shell::class);
    }
}
