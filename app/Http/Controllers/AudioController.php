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
                "$audio->path", 'public');
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
                    'type' => 'AudioView',
                    'path' => (Storage::disk('converts')->exists($audio_path)) ?
                        Storage::disk('converts')->getDriver()->getAdapter()->getPathPrefix() . $audio_path : null
                ]
            );
        } else
            abort(404);
    }

    public function edit($edit_facet_id)
    {
        $edit_facet = AudioEditFacet::findOrFail($edit_facet_id);

        return response()->json(
            [
                'type' => 'AudioEdit',
                'view_facet' => route('audio.show', ['audio' => $edit_facet->audio->viewFacet->swiss_number]),
                'path' => (Storage::disk('converts')->exists($edit_facet->audio->path)) ?
                    Storage::disk('converts')->getDriver()->getAdapter()->getPathPrefix() . $edit_facet->audio->path : '',
                'delete' => route('audio.destroy', ['audio' => $edit_facet->swiss_number])
            ]
        );
    }

    public function destroy($facet_id)
    {
        $audio_edit = AudioEditFacet::findOrFail($facet_id);

        if (Storage::disk('converts')->exists($audio_edit->audio->path)) {
            Storage::disk('converts')->delete($audio_edit->audio->path);
            $audio_edit->audio->delete();
            return response('', 200);
        } else
            abort(404);
    }
}
