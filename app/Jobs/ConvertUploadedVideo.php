<?php

namespace App\Jobs;

use FFMpeg;

use App\Models\Media;

use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;

use FFMpeg\Coordinate\Dimension;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ConvertUploadedVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $video;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Media $media)
    {
        $this->video = $media;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $video_path = swiss_number();
        $bitRateFormat = (new X264('libmp3lame', 'libx264'))->setKiloBitrate(1500); // create a file format

        FFMpeg::fromDisk('uploads') // open the uploaded audio from the right disk
            ->open($this->video->path)
            ->export()
            ->toDisk('converts') // tell the Exporter to which disk we want to export
            ->inFormat($bitRateFormat)
            ->save("$video_path.mp4");

        Storage::disk('uploads')
            ->delete($this->video->path);

        $this->video->path = "$video_path.mp4";
        $this->video->save();
    }
}
