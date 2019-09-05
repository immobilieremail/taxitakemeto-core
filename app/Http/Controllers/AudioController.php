<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Audio,
    App\AudioListEditFacet,
    App\AudioListViewFacet;

use App\Jobs\ConvertUploadedAudio;

use App\Http\Requests\NewAudioRequest;

class AudioController extends Controller
{
    // need dispatch method from Controller
    private function convert(Audio $audio)
    {
        $this->dispatch(new ConvertUploadedAudio($audio));
        $audio->path = "$audio->swiss_number.mp3";
        $audio->save();
    }

    public function store(NewAudioRequest $request)
    {
        if ($request->has('audio')) {
            $extension = $request->file('audio')->extension();
            $audio = Audio::create(['extension' => $extension]);
            $request->file('audio')->storeAs('storage/uploads',
                "$audio->swiss_number.$extension", 'public');
            $this->convert($audio);
            return response()->json(
                [
                    "type" => "ocap",
                    "ocapType" => "Audio",
                    "url" => "http://localhost:8000/api/audio/$audio->swiss_number"
                ]
            );
        } else
            abort(400);
    }

    public function destroy($audio_id)
    {
        $audio = Audio::find($audio_id);
        $condition = $audio != NULL
            && Storage::disk('converts')->exists($audio->path);

        if ($condition) {
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
