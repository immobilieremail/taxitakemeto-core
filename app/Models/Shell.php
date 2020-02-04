<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ShellDropboxFacet;


class Shell extends Model
{
    const DEFAULT_DROPBOX_PETNAME = '__public';

    /**
     * Create Shell facets on boot
     *
     */
    public static function boot()
    {
        parent::boot();

        static::created(function (Shell $shell) {
            $shell->userFacet()->save(new ShellUserFacet);
            $shell->inviteFacet()->save(new ShellInviteFacet);
            $shell->dropboxFacets()->save(new ShellDropboxFacet(['petname' => self::DEFAULT_DROPBOX_PETNAME]));
        });
    }

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
     * UserProfileFacet for Shell user
     *
     * @return [type] [description]
     */
    public function users()
    {
        return $this->belongsToMany(UserProfileFacet::class, 'facet_shell_user');
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
    public function dropboxFacets()
    {
        return $this->hasMany(ShellDropboxFacet::class, 'target_id')
                    ->where('type', 'App\Models\ShellDropboxFacet');
    }

    public function getDropbox($petname = self::DEFAULT_DROPBOX_PETNAME)
    {
        return $this->dropboxFacets()->where('petname', $petname)->first();
    }

    /**
     * InviteFacet for specific Shell
     *
     * @return [type] [description]
     */
    public function inviteFacet()
    {
        return $this->hasOne(ShellInviteFacet::class, 'target_id')
                    ->where('type', 'App\Models\ShellInviteFacet');
    }
}
