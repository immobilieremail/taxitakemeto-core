<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Audio,
    App\AudioList,
    App\JoinListAudio,
    App\AudioListEditFacet;

use Illuminate\Http\Testing\MimeType,
    Illuminate\Support\Facades\Storage;

class AudioListController extends Controller
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

    private function storeLocally(Request $request, $audio_id)
    {
        $file = $request->file('audio');
        $extension = $request->file('audio')->extension();
        $filename =  $audio_id . '.' . $extension;
        $file->storeAs('storage/uploads', $filename, 'public');
    }

    public function edit($lang, $edit_facet_id, $validation_msg = null, $status_code = 200)
    {
        $edit_facet = AudioListEditFacet::find($edit_facet_id);
        if ($edit_facet != NULL) {
            $audio_list = AudioList::find($edit_facet->id_list);
            return response(view('upload-audio', [
                'validation_msg' => $validation_msg,
                'edit_nbr' => $edit_facet_id,
                'lang' => $lang,
                'lists' => $audio_list->getAudios()]), $status_code);
        } else {
            return response(view('404'), 404);
        }
    }

    public function new_audio(Request $request, $lang, $edit_facet_id)
    {
        $edit_facet = AudioListEditFacet::find($edit_facet_id);
        if ($edit_facet != NULL) {
            $audio_list = AudioList::find($edit_facet->id_list);

            if ($this->isFileAudio($request) == true) {
                $audio = Audio::create([
                    'path' => '/storage/uploads/',
                    'extension' => $request->file('audio')->extension()]);
                $joinlstaudio = JoinListAudio::create([
                    'id_list' => $edit_facet->id_list,
                    'id_audio' => $audio->swiss_number]);
                $this->storeLocally($request, $audio->swiss_number);
                $validation_msg = __('uploadaudio_message.file_uploaded');
                $status_code = 201;
            } else {
                $validation_msg = __('uploadaudio_message.file_not_uploaded');
                $status_code = 404;
            }
            return $this->edit($lang, $edit_facet_id, $validation_msg, $status_code);
        } else {
            response(view('404'), 404);
        }
    }

    public function update(Request $request, $lang, $edit_facet_id, $audio_id)
    {
        if ($this->isFileAudio($request) == true) {
            $this->storeLocally($request, $audio_id);
            return $this->edit($lang, $edit_facet_id, __('uploadaudio_message.file_uploaded'), 303);
        } else {
            return $this->edit($lang, $edit_facet_id, __('uploadaudio_message.file_not_uploaded'), 400);
        }
    }

    public function destroy(Request $request, $lang, $swiss_number, $audio_id)
    {
        $audio = Audio::find($audio_id);

        if ($audio != NULL) {
            if (AudioListEditFacet::find($swiss_number) != NULL) {
                if (Storage::disk('public')->exists($audio->path)) {
                    Storage::disk('public')->delete($audio->path);
                    $audio->delete();
                    return redirect("/$lang/audiolist_edit/$swiss_number", 303);
                } else {
                    return response(view('404'), 404);
                }
            } else {
                return response(view('404'), 404);
            }
        } else {
            return response(view('404'), 404);
        }
    }
}
