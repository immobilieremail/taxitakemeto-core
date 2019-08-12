<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Shell,
    App\Audio,
    App\AudioList,
    App\ShellDropbox,
    App\JoinListAudio,
    App\JoinShellToMsg,
    App\JoinDropboxToMsg,
    App\AudioListEditFacet,
    App\AudioListViewFacet,
    App\ShellDropboxMessage;

use App\Jobs\ConvertUploadedAudio;

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
                    'extension' => $request->file('audio')->extension()]);
                $joinlstaudio = JoinListAudio::create([
                    'id_list' => $edit_facet->id_list,
                    'id_audio' => $audio->swiss_number]);
                $this->storeLocally($request, $audio->swiss_number);
                $this->dispatch(new ConvertUploadedAudio($audio));
                return redirect("/$lang/audiolist_edit/$edit_facet_id", 303);
            } else {
                return response(view('404'), 404);
            }
        } else {
            return response(view('404'), 404);
        }
    }

    public function update(Request $request, $lang, $edit_facet_id, $audio_id)
    {
        if ($this->isFileAudio($request) == true) {
            $this->storeLocally($request, $audio_id);
            return redirect("/$lang/audiolist_edit/$edit_facet_id", 303);
        } else {
            return response(view('404'), 404);
        }
    }

    public function destroy(Request $request, $lang, $swiss_number, $audio_id)
    {
        $audio = Audio::find($audio_id);

        if ($audio != NULL) {
            if (AudioListEditFacet::find($swiss_number) != NULL) {
                if (Storage::disk('converts')->exists($audio->path)) {
                    Storage::disk('converts')->delete($audio->path);
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

    private function share_check_type(String $type, Int $id_list): ShellDropboxMessage
    {
        if ($type == "RW") {
            $new_audiolist_facet = AudioListEditFacet::create([
                'id_list' => $id_list]);
        } else if ($type == "RO") {
            $new_audiolist_facet = AudioListViewFacet::create([
                'id_list' => $id_list]);
        } else
            return NULL;
        return ShellDropboxMessage::create([
            'capability' => $new_audiolist_facet->swiss_number,
            'type' => $type . "FAL"]);
    }

    public function share(Request $request, $lang, $edit_facet_id)
    {
        $audiolist_edit_facet = AudioListEditFacet::find($edit_facet_id);
        $shell_dropbox = ShellDropbox::find($request->dropbox);
        $shell = Shell::find($request->shell_id);

        if ($audiolist_edit_facet != NULL) {
            if ($shell_dropbox != NULL) {
                $msg = $this->share_check_type($request->result, $audiolist_edit_facet->id_list);
                JoinDropboxToMsg::create([
                    'id_dropbox' => $shell_dropbox->swiss_number,
                    'id_msg' => $msg->swiss_number
                ]);
                JoinShellToMsg::create([
                    'id_shell' => $shell->swiss_number,
                    'id_msg' => $msg->swiss_number
                ]);
                return back();
            } else {
                return response(view('404'), 404);
            }
        } else {
            return response(view('404'), 404);
        }
    }
}
