<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Audio,
    App\AudioEditFacet,
    App\AudioViewFacet;

use App\Jobs\ConvertUploadedAudio;

use App\Http\Requests\NewAudioRequest;

class AudioController extends Controller
{
    // need dispatch method from Controller
    private function convert(Audio $audio)
    {
        $this->dispatch(new ConvertUploadedAudio($audio));
        $audio->path = "$audio->id.mp3";
        $audio->save();
    }

    public function store(NewAudioRequest $request)
    {
        if ($request->has('audio')) {
            $extension = $request->file('audio')->extension();
            $audio = Audio::create(['extension' => $extension]);
            $audio_edit = AudioEditFacet::create(["id_audio" => $audio->id]);
            $audio_view = AudioViewFacet::create(["id_audio" => $audio->id]);

            $request->file('audio')->storeAs('storage/uploads',
                "$audio->id.$extension", 'public');
            $this->convert($audio);
            return response()->json(
                [
                    "type" => "ocap",
                    "ocapType" => "Audio",
                    "url" => "/api/audio/$audio_edit->swiss_number/edit"
                ]
            );
        } else
            abort(400);
    }

    public function show($facet_id)
    {
        $edit_facet = AudioEditFacet::find($facet_id);
        $view_facet = AudioViewFacet::find($facet_id);
        $facet = ($view_facet != NULL) ?
            $view_facet : $edit_facet;
        $facet_type = ($view_facet != NULL) ?
            "AudioView" : "AudioEdit";

        if ($facet != NULL) {
            return response()->json(
                [
                    "type" => $facet_type,
                    "id" => $facet_id,
                    "contents" => ""
                ]
            );
        } else
            abort(404);
    }

    public function edit($edit_facet_id)
    {
        $edit_facet = AudioEditFacet::find($edit_facet_id);

        if ($edit_facet != NULL) {
            return response()->json(
                [
                    "type" => "AudioEdit",
                    "id" => $edit_facet_id,
                    "view_facet" => "/api/audio/" . $edit_facet->getViewFacet()->swiss_number,
                    "contents" => "",
                    "delete_audio" => "/api/audio/$edit_facet->swiss_number"
                ]
            );
        } else
            abort(404);
    }

    public function destroy($facet_id)
    {
        $audio_edit = AudioEditFacet::find($facet_id);
        $audio = ($audio_edit != NULL) ?
            Audio::find($audio_edit->id_audio) : NULL;

        if ($audio != NULL && Storage::disk('converts')->exists($audio->path)) {
            Storage::disk('converts')->delete($audio->path);
            $audio->delete();
            return response()->json(
                [
                    'status' => 200
                ]
            );
        } else
            abort(404);
    }
}
