<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use App\Models\MediaEditFacet;
use App\Models\MediaViewFacet;
use App\Jobs\ConvertUploadedAudio;
use App\Jobs\ConvertUploadedImage;
use App\Jobs\ConvertUploadedVideo;

class MediaController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->has('media') == false) {
            return response('Bad Request', 400);
        }
        elseif ($request->file('media') == null) {
            return response('Unsupported Media Type', 415);
        }
        elseif (!in_array(substr($request->file('media')->getMimeType(), 0, 6), ['audio/', 'video/', 'image/'], true)) {
            return response('Unsupported Media Type', 415);
        }

        $media_name = swissNumber() . '.' . $request->file('media')->extension();
        $path = $request->file('media')->storeAs('', $media_name, 'uploads');
        $mime_type = $request->file('media')->getMimeType();

        $media_type = [];
        preg_match('#(^[a-z])\w+#', $mime_type, $media_type);

        $media = Media::create(['path' => $path, 'media_type' => $media_type[0]]);

        if ($media_type[0] == 'audio') {
            $this->dispatch(new ConvertUploadedAudio($media));
        }
        elseif ($media_type[0] == 'video') {
            $this->dispatch(new ConvertUploadedVideo($media));
        }
        elseif ($media_type[0] == 'image') {
            $this->dispatch(new ConvertUploadedImage($media));
        }
        return response()->json([
            'type' => 'ocap',
            'ocapType' => 'MediaEditFacet',
            'url' => route('obj.show', ['obj' => $media->editFacet->id])
        ]);
    }
}
