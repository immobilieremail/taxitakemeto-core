<?php

namespace App\Models;

use Illuminate\Http\Request;

class ShellInviteFacet extends Facet
{
    /**
     * Facet method permissions
     * @var array
     */
    protected $permissions      = [
        'store', 'show'
    ];

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
     * Inverse relation of InviteFacet for specific Shell
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(Shell::class);
    }

    public function post_data(Request $request): array
    {
        $shell = Shell::create();
        $shell->userFacet()->save(new ShellUserFacet);
        $shell->inviteFacet()->save(new ShellInviteFacet);
        $shell->dropboxFacet()->save(new ShellDropboxFacet);

        $this->target->dropboxFacet->sent_invitations()->save(
            Invitation::make(['recipient' => $shell->dropboxFacet->id])
        );

        return [];
    }

    public function description(): array
    {
        $userFacet = $this->target->users->first();

        return [
            'type' => 'ShellInviteFacet',
            'url' => route('obj.show', ['obj' => $this->id]),
            'data' => [
                'name' => ($userFacet != null)
                    ? $userFacet->target->name : null,
                'dropbox' => route('obj.show', ['obj' => $this->target->dropboxFacet->id])
            ]
        ];
    }
}
