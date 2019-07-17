<?php

namespace App\Http\Controllers;

use App\Edit,
    App\View,
    App\Sound,
    App\JoinListSound,
    App\Http\Requests\AudiosRequest,
    Illuminate\Http\Testing\MimeType;

require_once __DIR__ . "/myfunctions/rand_nbr.php";

class UploadAudioController extends Controller
{
    public function index()
    {
        return redirect('/');
    }

    private function storeLocally(AudiosRequest $request, $random_nbr)
    {
        $file = $request->file('audio');
        $extension = $request->file('audio')->extension();
        $filename =  $random_nbr . '.' . $extension;
        $file->move('storage/uploads', $filename);
        return ($filename);
    }

    public function store(AudiosRequest $request, $suisse_nbr)
    {
        $failed_view = view('upload-audio', ['validation_msg' => 'File upload failed.', 'nbr' => $suisse_nbr]);
        $success_view = view('upload-audio', ['validation_msg' => 'File has been successfully uploaded.', 'nbr' => $suisse_nbr]);

        if ($request->has('audio')) {
            $extension = $request->file('audio')->extension();
            $mime = MimeType::get($extension);
            if (strpos($mime, 'audio') === false)
                return $failed_view;

            $random_nbr = rand_large_nbr();

            $filename = $this->storeLocally($request, $random_nbr);

            $return_value = Sound::addToDB($random_nbr, $filename);
            if ($return_value !== true)
                return $failed_view;

            $view_nbr = Edit::getViewNbr($suisse_nbr);
            if ($view_nbr == 0)
                return $failed_view;

            $soundlist_nbr = View::getSoundListNbr($view_nbr);
            if ($soundlist_nbr == 0)
                return $failed_view;

            $return_value = JoinListSound::addToDB($random_nbr, $soundlist_nbr);
            if ($return_value == true)
                return $success_view;
        }
        return $failed_view;
    }

    public function show($suisse_nbr)
    {
        $count = 0;
        $edits = Edit::getEdits($suisse_nbr);

        foreach ($edits as $edit)
            $count += 1;
        if ($count !== 1)
            return view('404');
        return view('upload-audio', ['validation_msg' => '', 'nbr' => $suisse_nbr]);
    }
}
