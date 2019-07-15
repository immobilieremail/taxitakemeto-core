<?php

namespace App\Http\Controllers;

use App\Audio;
use Illuminate\Support\Str;
use App\Http\Requests\AudiosRequest;
use Illuminate\Support\Facades\Auth;


class UploadAudioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('upload-audio', ['validation_msg' => '']);
    }

    public function store(AudiosRequest $request)
    {
        if ($request->has('audio')) {
            $extension = $request->file('audio')->getClientOriginalExtension();
            if ($extension == 'wav'
                || $extension == 'mp3' || $extension == 'ogg') {
                $file = $request->file('audio');
                $original_name = str_replace(('.' . $extension), '',
                    $request->file('audio')->getClientOriginalName());
                $filename = time() . '_' . $original_name  . '_' . uniqid() . '.' . $extension;
                $file->move('storage/uploads', $filename);

                $audio = new Audio;

                $audio->name = $original_name;
                $audio->path = 'storage/uploads/' . $filename;
                $audio->owner_id = Auth::user()->owner_id;
                $audio->audio_id = Str::random(100);
                $audio->save();

                return view('upload-audio', ['validation_msg' => 'File has been successfully uploaded.']);
            }
        }
        return view('upload-audio', ['validation_msg' => 'File upload failed.']);
    }
}
