<?php

namespace App\Jobs;

use App\Audio;

use Illuminate\Support\Facades\Storage;

use FFMpeg;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Audio\Mp3;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ConvertUploadedAudio implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $audio;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Audio $audio)
    {
        $this->audio = $audio;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $bitRateFormat = (new Mp3)->setAudioKiloBitrate(1000); // create a file format

        FFMpeg::fromDisk('uploads') // open the uploaded audio from the right disk
            ->open($this->audio->path)
            ->export()
            ->toDisk('converts') // tell the Exporter to which disk we want to export
            ->inFormat($bitRateFormat)
            ->save($this->audio->id . '.mp3');

        Storage::disk('uploads')
            ->delete($this->audio->path);
    }
}
