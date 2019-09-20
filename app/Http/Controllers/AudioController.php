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
    public function store(NewAudioRequest $request)
    {
        if ($request->has('audio')) {
            $extension = $request->file('audio')->extension();
            $audio = Audio::create(['extension' => $extension]);
            $audio_edit = AudioEditFacet::create(["id_audio" => $audio->id]);
            $audio_view = AudioViewFacet::create(["id_audio" => $audio->id]);

            $request->file('audio')->storeAs('storage/uploads',
                "$audio->id.$extension", 'public');
            $this->dispatch(new ConvertUploadedAudio($audio));
            return response()->json(
                [
                    "type" => "ocap",
                    "ocapType" => "AudioEdit",
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
        $facet = ($view_facet != null) ?
            $view_facet : $edit_facet;

        if ($facet != null) {
            $audio_path = Audio::find($facet->id_audio)->path;
            return response()->json(
                [
                    "type" => "AudioView",
                    "contents" => (Storage::disk('converts')->exists($audio_path)) ?
                        Storage::disk('converts')->getDriver()->getAdapter()->getPathPrefix() . $audio_path : null
                ]
            );
        } else
            abort(404);
    }

    public function edit($edit_facet_id)
    {
        $edit_facet = AudioEditFacet::find($edit_facet_id);

        if ($edit_facet != null) {
            $audio_path = Audio::find($edit_facet->id_audio)->path;
            return response()->json(
                [
                    "type" => "AudioEdit",
                    "view_facet" => "/api/audio/" . $edit_facet->getViewFacet()->swiss_number,
                    "contents" => (Storage::disk('converts')->exists($audio_path)) ?
                        Storage::disk('converts')->getDriver()->getAdapter()->getPathPrefix() . $audio_path : null,
                    "delete_audio" => "/api/audio/$edit_facet->swiss_number"
                ]
            );
        } else
            abort(404);
    }

    public function destroy($facet_id)
    {
        $audio_edit = AudioEditFacet::find($facet_id);
        $audio = ($audio_edit != null) ?
            Audio::find($audio_edit->id_audio) : null;

        if ($audio != null && Storage::disk('converts')->exists($audio->path)) {
            Storage::disk('converts')->delete($audio->path);
            $audio->delete();
            return response('', 200);
        } else
            abort(404);
    }
}
