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
    protected $app_url = "http://localhost:8000";

    public function store()
    {
        $audiolist = AudioList::create();
        $audiolist_view = AudioListViewFacet::create(['id_list' => $audiolist->id]);
        $audiolist_edit = AudioListEditFacet::create(['id_list' => $audiolist->id]);

        return json_encode(array(
            "type" => "ocap",
            "ocapType" => "ALEdit",
            "url" => "$this->app_url/api/audiolist_edit/$audiolist_edit->swiss_number"));
    }

    public function view($view_facet_id)
    {
        $view_facet = AudioListViewFacet::find($view_facet_id);

        if ($view_facet != NULL) {
            $audio_array = $view_facet->getAudios();
            $return_contents = array();
            foreach ($audio_array as $audio) {
                $tmp_audio = array(
                    "type" => "Audio",
                    "audio_id" => $audio->swiss_number,
                    "path_to_file" => str_replace(public_path(), '', Storage::disk('converts')->path($audio->path)));
                $tmp_array = array("audio" => $tmp_audio);
                array_push($return_contents, $tmp_array);
            }
            return json_encode(array(
                "type" => "ALView",
                "contents" => $return_contents));
        } else
            abort(404);
    }

    public function edit($edit_facet_id)
    {
        $edit_facet = AudioListEditFacet::find($edit_facet_id);

        if ($edit_facet != NULL) {
            $audio_array = $edit_facet->getAudios();
            $return_contents = array();
            foreach ($audio_array as $audio) {
                $tmp_audio = array(
                    "type" => "Audio",
                    "audio_id" => $audio->swiss_number,
                    "path_to_file" => str_replace(public_path(), '', Storage::disk('converts')->path($audio->path)));
                $tmp_array = array(
                    "audio" => $tmp_audio,
                    "update_audio" => "$this->app_url/api/audiolist_edit/$edit_facet_id/audio/$audio->swiss_number",
                    "delete_audio" => "$this->app_url/api/audiolist_edit/$edit_facet_id/audio/$audio->swiss_number");
                array_push($return_contents, $tmp_array);
            }
            return json_encode(array(
                "type" => "ALEdit",
                "delete" => "$this->app_url/api/audiolist_edit/$edit_facet_id",
                "new_audio" => "$this->app_url/api/audiolist_edit/$edit_facet_id/new_audio",
                "view_facet" => "$this->app_url/api/audiolist_view/" . $edit_facet->getViewFacet()->swiss_number,
                "contents" => $return_contents));
        } else
            abort(404);
    }

    private function isFileAudio(Request $request)
    {
        $matches = array();

        if ($request->has('audio')) {
            $extension = $request->file('audio')->extension();
            $mime = MimeType::get($extension);
            preg_match('#^([^/]+)/#', $mime, $matches);
            return $matches[0] == 'audio/';
        } else
            return false;
    }

    private function storeLocally(Request $request, $audio_id)
    {
        $file = $request->file('audio');
        $extension = $request->file('audio')->extension();
        $filename =  $audio_id . '.' . $extension;
        $file->storeAs('storage/uploads', $filename, 'public');
    }

    public function new_audio(Request $request, $edit_facet_id)
    {
        $edit_facet = AudioListEditFacet::find($edit_facet_id);

        if ($edit_facet != NULL && $this->isFileAudio($request) == true) {
            $audio = $edit_facet->addAudio($request->file('audio')->extension());
            $this->storeLocally($request, $audio->swiss_number);
            $this->dispatch(new ConvertUploadedAudio($audio));
            return json_encode(array(
                "type" => "Audio",
                "audio_id" => $audio->swiss_number,
                "path_to_file" => $audio->path));
        } else
            abort(404);
    }

    public function update(Request $request, $edit_facet_id, $audio_id)
    {
        $audio = Audio::find($audio_id);
        $edit_facet = AudioListEditFacet::find($edit_facet_id);
        $condition = $edit_facet != NULL && $audio != NULL;

        if ($condition && $this->isFileAudio($request) == true) {
            $audio->path = str_replace('.mp3',
                '.' . $request->file('audio')->extension(), $audio->path);
            $audio->save();
            $this->storeLocally($request, $audio->swiss_number);
            $this->dispatch(new ConvertUploadedAudio($audio));
            return json_encode(array(
                "type" => "Audio",
                "audio_id" => $audio->swiss_number,
                "path_to_file" => $audio->path));
        } else
            abort(404);
    }

    public function list_destroy($audiolist_edit_id)
    {
        $audiolist_edit = AudioListEditFacet::find($audiolist_edit_id);

        if ($audiolist_edit != NULL) {
            $audiolist = AudioList::find($audiolist_edit->id_list);
            $audios = $audiolist_edit->getAudios();
            foreach ($audios as $audio) {
                Storage::disk('converts')->delete($audio->path);
                $audio->delete();
            }
            $audiolist->delete();
            return 200;
        } else
            return abort(404);
    }

    public function audio_destroy($swiss_number, $audio_id)
    {
        $audio = Audio::find($audio_id);
        $condition = $audio != NULL
            && AudioListEditFacet::find($swiss_number) != NULL
            && Storage::disk('converts')->exists($audio->path);

        if ($condition) {
            Storage::disk('converts')->delete($audio->path);
            $audio->delete();
            return 200;
        } else
            abort(404);
    }
}
