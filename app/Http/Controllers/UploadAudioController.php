<?php

namespace App\Http\Controllers;

use App\Shell,
    App\Audio,
    App\AudioList,
    App\JoinShellView,
    App\JoinShellEdit,
    App\JoinListAudio,
    App\AudioListViewFacet,
    App\AudioListEditFacet,
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
        $audio = Audio::addToDB($random_nbr, $filename);
        if ($audio == null)
            return false;

        $audiolist_nbr = AudioListViewFacet::find($view_nbr)->id_list;

        $joinlstaudio = JoinListAudio::addToDB($random_nbr, $audiolist_nbr);
        if ($joinlstaudio !== null)
            return true;
        return false;
    }

    private function getAllAudios($audiolist_id)
    {
        $audios = array();

        $joinlstaudio = JoinListAudio::all()->where('id_list', $audiolist_id);
        foreach ($joinlstaudio as $join)
            array_push($audios, Audio::find($join->id_audio));
        return $audios;
    }

    public function index($lang, $suisse_nbr)
    {
        $edit = AudioListEditFacet::find($suisse_nbr);
        $view_404 = response(view('404'), 404);

        if (!isset($edit))
            return $view_404;
        $audiolist = AudioList::find($edit->id_list);
        if ($audiolist == NULL)
            return $view_404;
        $view = AudioListViewFacet::where('id_list', $edit->id_list)->first();
        return view('upload-audio', [
            'validation_msg' => '',
            'edit_nbr' => $edit->id,
            'view_nbr' => $view->id,
            'lang' => $lang,
            'lists' => $this->getAllAudios($audiolist->id)]);
    }

    public function store(Request $request, $lang, $suisse_nbr)
    {
        $list_id = AudioListEditFacet::find($suisse_nbr)->id_list;
        $view_id = AudioListViewFacet::where('id_list', $list_id)->first()->id;
        $failed_view = response(view('upload-audio', [
            'validation_msg' => __('uploadaudio_message.file_not_uploaded'),
            'edit_nbr' => $suisse_nbr,
            'view_nbr' => $view_id,
            'lang' => $lang,
            'lists' => $this->getAllAudios($list_id)]), 400);

        if ($this->isFileAudio($request) == true) {
            $random_nbr = rand_large_nbr();
            $filename = $this->storeLocally($request, $random_nbr);
            if ($this->insertIntoDB($random_nbr, $filename, $view_id) == false)
                return $failed_view;
            return response(view('upload-audio', [
                    'validation_msg' => __('uploadaudio_message.file_uploaded'),
                    'edit_nbr' => $suisse_nbr,
                    'view_nbr' => $view_id,
                    'lang' => $lang,
                    'lists' => $this->getAllAudios($list_id)]), 201);
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
        $audio = Audio::find($audio_id);
        $dir_path = '/home/louis/audio_handler/public';

        if (Audio::deleteFromDB($audio_id) == true) {
            if (file_exists($dir_path . $audio->path))
                unlink($dir_path . $request->audio_path);
            return redirect("/$lang/upload-audio/$suisse_nbr", 303);
        } else {
            return response(view('404'), 404);
        }
    }
}
