<?php

namespace App\Models;

class ShellInviteFacet extends Facet
{
    /**
     * Facet method permissions
     * @var array
     */
    protected $permissions      = [
        'show'
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

    public function description()
    {
        $userFacet = $this->target->users->first();

        return [
            'type' => 'ShellInviteFacet',
            'url' => route('obj.show', ['obj' => $this->id]),
            'data' => [
                'name' => ($userFacet != null)
                    ? $userFacet->target->name : null,
                'dropbox' => $this->target->dropboxFacet
            ]
        ];
    }
}
