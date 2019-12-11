<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;

use App\Models\User;
use App\Models\UserProfileFacet;

class UserController extends Controller
{
    public function store(UserRequest $request)
    {
        $user = User::create($request->all());
        $user->profileFacet()->save(new UserProfileFacet);

        return response()->json([
            'type' => 'ocap',
            'ocapType' => 'UserProfileFacet',
            'url' => route('obj.show', ['obj' => $user->profileFacet->id])
        ]);
    }
}
