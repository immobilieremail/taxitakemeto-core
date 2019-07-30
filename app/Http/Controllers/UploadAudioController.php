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

    private function insertIntoDB($random_nbr, $filename, $audiolist_nbr)
    {
        $audio = Audio::addToDB($random_nbr, $filename);
        if ($audio == null)
            return false;

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
        $view_nbr = NULL;
        $edit = AudioListEditFacet::find($suisse_nbr);
        $view_404 = response(view('404'), 404);

        if (!isset($edit))
            return $view_404;
        $shell_id = $edit->id_shell;
        $audiolist = AudioList::find($edit->id_list);
        if ($audiolist == NULL)
            return $view_404;
        $view_nbr = AudioListViewFacet::getViewIDIfPossible($edit->id_list, $shell_id);
        return view('upload-audio', [
            'validation_msg' => '',
            'edit_nbr' => $edit->id,
            'view_nbr' => $view_nbr,
            'lang' => $lang,
            'lists' => $this->getAllAudios($audiolist->id)]);
    }

    public function store(Request $request, $lang, $suisse_nbr)
    {
        $view_nbr = NULL;
        $status_code = 404;
        $edit = AudioListEditFacet::find($suisse_nbr);
        $validation_message = __('uploadaudio_message.file_not_uploaded');
        $view_nbr = AudioListViewFacet::getViewIDIfPossible($edit->id_list, $edit->id_shell);

        if ($this->isFileAudio($request) == true) {
            $random_nbr = rand_large_nbr();
            $filename = $this->storeLocally($request, $random_nbr);
            if ($this->insertIntoDB($random_nbr, $filename, $edit->id_list) != false) {
                $validation_message = __('uploadaudio_message.file_uploaded');
                $status_code = 201;
            }
        }
        return response(view('upload-audio', [
            'validation_msg' => $validation_message,
            'edit_nbr' => $suisse_nbr,
            'view_nbr' => $view_nbr,
            'lang' => $lang,
            'lists' => $this->getAllAudios($edit->id_list)]), $status_code);
    }

    public function share(Request $request, $lang, $suisse_nbr)
    {
        $new_view = NULL;
        $new_edit = NULL;
        $shell_to_share = Shell::find($request->share_to);
        $list_id = AudioListEditFacet::find($suisse_nbr)->id_list;

        if ($shell_to_share == NULL) {
            return back();
        } else {
            if ($request->view == true || $request->edit == true) {
                $new_view = AudioListViewFacet::addToDB(rand_large_nbr(), $list_id, $shell_to_share->id);
                if ($new_view == NULL)
                    return back();
            }
            if ($request->edit == true) {
                $new_edit = AudioListEditFacet::addToDB(rand_large_nbr(), $list_id, $shell_to_share->id);
                if ($new_edit == NULL)
                    return back();
            }
            return back(303);
        }
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

        if (AudioListEditFacet::find($suisse_nbr) != NULL && Audio::deleteFromDB($audio_id) == true) {
            if (file_exists($dir_path . $audio->path))
                unlink($dir_path . $request->audio_path);
            return redirect("/$lang/upload-audio/$suisse_nbr", 303);
        } else {
            return response(view('404'), 404);
        }
    }
}
