<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJoinListAudiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('join_list_audios', function (Blueprint $table) {
            $table->bigInteger('id_list')->unsigned();
            $table->bigInteger('id_audio');
            $table->timestamps();

            $table->foreign('id_list')
                ->references('id')
                ->on('audio_lists')
                ->onDelete('cascade');

            $table->foreign('id_audio')
                ->references('id')
                ->on('audios')
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
        Schema::dropIfExists('join_list_audios');
    }
}
