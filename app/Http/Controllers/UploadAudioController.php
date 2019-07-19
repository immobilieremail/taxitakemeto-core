<?php

namespace App\Http\Controllers;

use App\Edit,
    App\View,
    App\Sound,
    App\SoundList,
    App\QueueList,
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
            preg_match("#^([^/]+)/#", $mime, $matches);
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

    private function getQueueNbr($view_nbr)
    {
        $nbr_queue = 0;

        $view = View::where('id_view', $view_nbr)->first();
        $queues = QueueList::all()->where('id_list', $view->id_list);
        foreach ($queues as $queue)
            $nbr_queue += 1;
        return $nbr_queue;
    }

    public function index($suisse_nbr)
    {
        $count = 0;
        $nbr_queue = 0;
        $edits = Edit::getEdits($suisse_nbr);

        foreach ($edits as $edit) {
            $count += 1;
            $view_nbr = $edit->id_view;
        }
        if ($count !== 1)
            return view('404');
        $nbr_queue = $this->getQueueNbr($view_nbr);
        return view('upload-audio', [
            'validation_msg' => '',
            'edit_nbr' => $suisse_nbr,
            'view_nbr' => $view_nbr,
            'queue' => $nbr_queue,
            'lists' => $this->getAllAudios($suisse_nbr)]);
    }

    public function store(Request $request, $suisse_nbr)
    {
        $view_nbr = Edit::getViewNbr($suisse_nbr);
        $nbr_queue = $this->getQueueNbr($view_nbr);
        $failed_view = view('upload-audio', [
            'validation_msg' => 'File upload failed.',
            'edit_nbr' => $suisse_nbr,
            'view_nbr' => $view_nbr,
            'queue' => $nbr_queue,
            'lists' => $this->getAllAudios($suisse_nbr)]);

        if ($this->isFileAudio($request) == true) {
            $random_nbr = rand_large_nbr();
            $filename = $this->storeLocally($request, $random_nbr);
            $model = QueueList::insertIntoDB(View::where('id_view', $view_nbr)->first()->id_list, $random_nbr, $filename);
            if ($model == false)
                return $failed_view;
            $nbr_queue += 1;
            return view('upload-audio', [
                    'validation_msg' => 'File has been successfully uploaded. It will be checked by moderators.',
                    'edit_nbr' => $suisse_nbr,
                    'view_nbr' => $view_nbr,
                    'queue' => $nbr_queue,
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
        Sound::findOrFail($audio_id)->delete();
        unlink('/home/louis/audio_handler/public' . $request->audio_path);
        return back();
    }
}
