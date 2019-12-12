<?php

namespace App\Models;

use Illuminate\Http\Request;

class ShellDropboxFacet extends Facet
{
    /**
     * Facet method permissions
     * @var array
     */
    protected $permissions      = [];

    /**
     * Check if Facet has permissions for specific request method
     *
     * @return bool permission
     */
    public function has_access(String $method): bool
    {
        return in_array($method, $this->permissions, true);
    }

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
