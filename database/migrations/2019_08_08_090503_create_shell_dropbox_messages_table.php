<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShellDropboxMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shell_dropbox_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('id_receiver', 24);
            $table->string('capability', 24);
            $table->enum('type', ['ROFAL', 'RWFAL']); // ReadOnlyFacetAudioList, ReadWriteFacetAudioList
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shell_dropbox_messages');
    }
}
