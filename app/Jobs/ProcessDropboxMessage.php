<?php

namespace App\Jobs;

use App\Shell;
use App\ShellDropboxFacet;
use App\AudioListEditFacet;
use App\AudioListViewFacet;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessDropboxMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Array $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $shell = Shell::find($this->message["dropbox"]->id_shell);

        $this->message["facet"]->shells()->save($shell);
    }
}
