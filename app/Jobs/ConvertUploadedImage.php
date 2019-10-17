<?php

namespace App\Jobs;

use Spatie\Glide\GlideImage;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ConvertUploadedImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $image;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Media $media)
    {
        $this->image = $media;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $image_path = swiss_number();
        $url = Storage::url($this->image->path);
        $path = Storage::disk('converts')->getAdapter()->getPathPrefix();

        GlideImage::create($url)
            ->modify(['fm'=>'jpg'])
            ->save($path . $image_path . '.jpg');

        Storage::disk('uploads')
            ->delete($this->image->path);

        $this->image->path = "$image_path.jpg";
        $this->image->save();
    }
}
