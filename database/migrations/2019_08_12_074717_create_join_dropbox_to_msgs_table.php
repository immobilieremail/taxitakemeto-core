<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJoinDropboxToMsgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('join_dropbox_to_msgs', function (Blueprint $table) {
            $table->string('id_dropbox', 24);
            $table->string('id_msg', 24);
            $table->timestamps();

            $table->foreign('id_dropbox')
                ->references('swiss_number')
                ->on('shell_dropboxes')
                ->onDelete('cascade');

            $table->foreign('id_msg')
                ->references('swiss_number')
                ->on('shell_dropbox_messages')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('join_dropbox_to_msgs');
    }
}
