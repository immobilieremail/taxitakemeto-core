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
     * Inverse relation of InviteFacet for specific Shell
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(Shell::class);
    }

    public function create_invitation()
    {
        $new_shell = Shell::create();

        $this->target->dropboxFacet->sent_invitations()->save(
            Invitation::make(['recipient' => $new_shell->dropboxFacet->id])
        );

        if ($new_shell->exists()) {
            return [];
        } else {
            return false;
        }
    }

    public function post_data(Request $request): array
    {
        return $this->create_invitation();
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
