<?php

namespace App\Models;

use Illuminate\Http\Request;

class ShellDropboxFacet extends Facet
{
    /**
     * Facet method permissions
     * @var array
     */
    protected $permissions      = [
        'show'
    ];

    protected $fillable = [ 'petname' ];

    /**
     * Inverse relation of DropboxFacet for specific Shell
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(Shell::class);
    }

    public function description()
    {
        $userFacet = $this->target->users->first();

        return [
            'name' => ($userFacet != null) ? $userFacet->target->name : null
        ];
    }
}
