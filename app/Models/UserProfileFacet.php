<?php

namespace App\Models;

use Illuminate\Http\Request;

class UserProfileFacet extends Facet
{
     /**
     * Facet method permissions
     * @var array
     */
    protected $permissions      = [
        'show', 'update'
    ];

    /**
     * Inverse relation of ProfileFacet for specific user
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(User::class);
    }

    public function description()
    {
        return [
            'type' => 'UserProfileFacet',
            'url' => route('obj.show', ['obj' => $this->id]),
            'data' => [
                'name' => $this->target->name,
                'email' => $this->target->email,
                'phone' => $this->target->phone,
                'password' => $this->target->password
            ]
        ];
    }

    public function updateTarget(Request $request)
    {
        $allowed = ['name', 'email', 'phone', 'password'];
        $new_data = intersectFields($allowed, $request->all());
        $tested_data = array_filter($new_data, function ($value, $key) {
            $tests = [
                'name' => is_string($value),
                'email' => is_string($value),
                'phone' => is_string($value),
                'password' => is_string($value)
            ];

            return $tests[$key];
        }, ARRAY_FILTER_USE_BOTH);

        $this->target->update($tested_data);
        return !empty($tested_data);
    }
}
