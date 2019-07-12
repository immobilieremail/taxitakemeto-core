<?php

namespace App\Http\Controllers;

use App\Http\Requests\AudiosRequest;

class UploadAudioController extends Controller
{
    public function index()
    {
        return view('upload-audio');
    }

    public function store(AudiosRequest $request)
    {
        if ($request->has('audio')) {
            $uniqueid = uniqid();
            $extension = $request->file('audio')->getClientOriginalExtension();
            $original_name = str_replace(('.' . $extension), '', $request->file('audio')->getClientOriginalName());
            $filename = time() . '_' . $original_name  . '_' . $uniqueid . '.' . $extension;
            $request->file('audio')->storeAs('public/uploads', $filename);
            return 'Le nom du fichier est : ' . $filename . '.';
        } else {
            return 'Request has no audio named file.';
        }
    }
}
