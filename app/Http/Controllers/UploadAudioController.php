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
        $return_value = Sound::addToDB($random_nbr, $filename);
        if ($return_value !== true)
            return false;

        $soundlist_nbr = View::getSoundListNbr($view_nbr);

        $return_value = JoinListSound::addToDB($random_nbr, $soundlist_nbr);
        if ($return_value == true)
            return true;
        return false;
    }

    private function getAllAudios($suisse_nbr)
    {
        $audios = array();

        $edit = Edit::getFirstEdit($suisse_nbr);
        $view = View::getFirstView($edit->id_view);
        $list = SoundList::getFirstSoundList($view->id_list);
        if ($list == NULL)
            return NULL;

        $audios = getSounds($list->id);
        return $audios;
    }

    public function index($suisse_nbr)
    {
        $edit = Edit::getFirstEdit($suisse_nbr);
        $view_404 = view('404');

        $view_nbr = $edit->id_view;
        $view = View::where('id_view', $view_nbr)->first();
        $soundlist = SoundList::find($view->id_list);
        if ($soundlist == NULL)
            return $view_404;
        return view('upload-audio', [
            'validation_msg' => '',
            'edit_nbr' => $suisse_nbr,
            'view_nbr' => $view_nbr,
            'lists' => $this->getAllAudios($suisse_nbr)]);
    }

    public function store(Request $request, $suisse_nbr)
    {
        $view_nbr = Edit::getViewNbr($suisse_nbr);
        $failed_view = view('upload-audio', [
            'validation_msg' => 'File upload failed.',
            'edit_nbr' => $suisse_nbr,
            'view_nbr' => $view_nbr,
            'lists' => $this->getAllAudios($suisse_nbr)]);

        if ($this->isFileAudio($request) == true) {
            $random_nbr = rand_large_nbr();
            $filename = $this->storeLocally($request, $random_nbr);
            if ($this->insertIntoDB($random_nbr, $filename, $view_nbr) == false)
                return $failed_view;
            return view('upload-audio', [
                    'validation_msg' => 'File has been successfully uploaded.',
                    'edit_nbr' => $suisse_nbr,
                    'view_nbr' => $view_nbr,
                    'lists' => $this->getAllAudios($suisse_nbr)]);
        }
        return $failed_view;
    }

    public function update(Request $request, $suisse_nbr, $audio_id)
    {
        if ($this->isFileAudio($request) == true)
            $filename = $this->storeLocally($request, $audio_id);
        return back();
    }

    public function destroy(Request $request, $suisse_nbr, $audio_id)
    {
        $audio = Sound::find($audio_id);
        $dir_path = '/home/louis/audio_handler/public';

        if ($audio !== NULL) {
            if (file_exists($dir_path . $audio->path))
                unlink($dir_path . $request->audio_path);
            Sound::find($audio_id)->delete();
        }
        return back();
    }
}
