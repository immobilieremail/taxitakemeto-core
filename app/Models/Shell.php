<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shell extends Model
{
    /**
     * OcapList facets for Shell travel list
     *
     * @return [type] [description]
     */
    public function travelOcapListFacets()
    {
        return $this->belongsToMany(Facet::class, 'facet_shell_travel');
    }

    /**
     * OcapList facets for Shell contact list
     *
     * @return [type] [description]
     */
    public function contactOcapListFacets()
    {
        return $this->belongsToMany(Facet::class, 'facet_shell_contact');
    }

    /**
     * Inverse relation of UserProfileFacet for specific shell
     */
    public function user()
    {
        return $this->belongsTo(UserProfileFacet::class);
    }

    /**
     * UserFacet for specific Shell
     *
     * @return [type] [description]
     */
    public function userFacet()
    {
        return $this->hasOne(ShellUserFacet::class, 'target_id')
                    ->where('type', 'App\Models\ShellUserFacet');
    }

    /**
     * DropboxFacet for specific Shell
     *
     * @return [type] [description]
     */
    public function dropboxFacet()
    {
        return $this->hasOne(ShellDropboxFacet::class, 'target_id')
                    ->where('type', 'App\Models\ShellDropboxFacet');
    }
}
