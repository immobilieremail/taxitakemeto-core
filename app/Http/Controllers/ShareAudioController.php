<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Request as globalRequest;
use Illuminate\Http\Request;

class ShareAudioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $audio_id = globalRequest::get('id');

        return view('share-audio', ['id' => $audio_id]);
    }

    public function store(Request $request)
    {
        $audio_id = globalRequest::get('id');
        $users = User::all()->where('email', $request->input('share-to-email'));

        foreach ($users as $users)
            $nb_of_user += 1;
        if ($nb_of_user > 1)
            return back();
        else if ($users->owner_id == Auth::user()->owner_id)
            return back();
        $permissions = $users->permissions;
        if ($permissions)
            array_push($permissions, $audio_id);
        else
            $permissions = $audio_id;
        User::where('email', $request->input('share-to-email'))->update(['permissions' => $permissions]);
        return redirect('list-audio');
    }
}
