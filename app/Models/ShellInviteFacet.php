<?php

namespace App\Models;

use Illuminate\Http\Request;

class ShellInviteFacet extends Facet
{
    /**
     * Inverse relation of InviteFacet for specific Shell
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(Shell::class);
    }

    public function store(Request $request): array
    {
        return $this->target->inviteNewUser();
    }

    public function show(): array
    {
        $userFacet = $this->target->users->first();

        return [
            'type' => 'ShellInviteFacet',
            'url' => route('obj.show', ['obj' => $this->id]),
            'data' => [
                'name' => ($userFacet != null)
                    ? $userFacet->target->name : null,
                'dropbox' => route('obj.show', ['obj' => $this->target->getDropbox()->id])
            ]
        ];
    }
}
