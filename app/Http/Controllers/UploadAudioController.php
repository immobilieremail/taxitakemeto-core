<?php

namespace App\Http\Controllers;

use App\Edit,
    App\View,
    App\Sound,
    App\SoundList,
    App\JoinListSound,
    Illuminate\Http\Request,
    Illuminate\Support\Facades\File,
    Illuminate\Http\Testing\MimeType;

require_once __DIR__ . "/myfunctions/rand_nbr.php";
require_once __DIR__ . "/myfunctions/get_sound.php";

class UploadAudioController extends Controller
{
    private function isFileAudio(Request $request)
    {
        $matches = array();

        if ($request->has('audio')) {
            $extension = $request->file('audio')->extension();
            $mime = MimeType::get($extension);
            preg_match('#^([^/]+)/#', $mime, $matches);
            return $matches[0] == 'audio/';
        } else {
            return false;
        }
    }

    private function storeLocally(Request $request, $random_nbr)
    {
        $file = $request->file('audio');
        $extension = $request->file('audio')->extension();
        $filename =  $random_nbr . '.' . $extension;
        $file->move('storage/uploads', $filename);
        return ($filename);
    }

    private function insertIntoDB($random_nbr, $filename, $view_nbr)
    {
        $sound = Sound::addToDB($random_nbr, $filename);
        if ($sound == null)
            return false;

        $soundlist_nbr = View::findByID($view_nbr)->id_list;

        $joinlstsnd = JoinListSound::addToDB($random_nbr, $soundlist_nbr);
        if ($joinlstsnd !== null)
            return true;
        return false;
    }

    private function getAllAudios($suisse_nbr)
    {
        $audios = array();

        $edit = Edit::findByID($suisse_nbr);
        $view = View::findByID($edit->id_view);
        $list = SoundList::findByID($view->id_list);
        if ($list == NULL)
            return NULL;

        $audios = getSounds($list->id);
        return $audios;
    }

    public function index($lang, $suisse_nbr)
    {
        $edit = Edit::where('id_edit', $suisse_nbr)->first();
        $view_404 = response(view('404'), 404);

        if (!isset($edit))
            return $view_404;
        $view_nbr = $edit->id_view;
        $view = View::where('id_view', $view_nbr)->first();
        if ($view == NULL)
            return $view_404;
        $soundlist = SoundList::find($view->id_list);
        if ($soundlist == NULL)
            return $view_404;
        return view('upload-audio', [
            'validation_msg' => '',
            'edit_nbr' => $suisse_nbr,
            'view_nbr' => $view_nbr,
            'lang' => $lang,
            'lists' => $this->getAllAudios($suisse_nbr)]);
    }

    public function store(Request $request, $lang, $suisse_nbr)
    {
        $view_nbr = Edit::where('id_edit', $suisse_nbr)->first()->id_view;
        $failed_view = response(view('upload-audio', [
            'validation_msg' => __('uploadaudio_message.file_not_uploaded'),
            'edit_nbr' => $suisse_nbr,
            'view_nbr' => $view_nbr,
            'lang' => $lang,
            'lists' => $this->getAllAudios($suisse_nbr)]), 400);

        if ($this->isFileAudio($request) == true) {
            $random_nbr = rand_large_nbr();
            $filename = $this->storeLocally($request, $random_nbr);
            if ($this->insertIntoDB($random_nbr, $filename, $view_nbr) == false)
                return $failed_view;
            return response(view('upload-audio', [
                    'validation_msg' => __('uploadaudio_message.file_uploaded'),
                    'edit_nbr' => $suisse_nbr,
                    'view_nbr' => $view_nbr,
                    'lang' => $lang,
                    'lists' => $this->getAllAudios($suisse_nbr)]), 201);
        }
        return $failed_view;
    }

    public function update(Request $request, $lang, $suisse_nbr, $audio_id)
    {
        if ($this->isFileAudio($request) == true)
            $filename = $this->storeLocally($request, $audio_id);
        return back(303);
    }

    public function destroy(Request $request, $lang, $suisse_nbr, $audio_id)
    {
        $audio = Sound::find($audio_id);
        $dir_path = '/home/louis/audio_handler/public';

        if (Sound::deleteFromDB($audio_id) == true) {
            if (file_exists($dir_path . $audio->path))
                unlink($dir_path . $request->audio_path);
            return redirect("/$lang/upload-audio/$suisse_nbr", 303);
        } else {
            return response(view('404'), 404);
        }
    }
}
