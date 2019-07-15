<?php

namespace App\Http\Controllers;

use App\Http\Requests\AudiosRequest;

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
            $file = $request->file('audio');
            $extension = $request->file('audio')->getClientOriginalExtension();
            $original_name = str_replace(('.' . $extension), '', $request->file('audio')->getClientOriginalName());
            $filename = time() . '_' . $original_name  . '_' . uniqid() . '.' . $extension;
            $file->move('storage/uploads', $filename);
            return view('upload-audio', ['validation_msg' => 'File has been successfully uploaded.']);
        } else {
            return view('upload-audio', ['validation_msg' => 'File upload failed.']);
        }
    }
}
