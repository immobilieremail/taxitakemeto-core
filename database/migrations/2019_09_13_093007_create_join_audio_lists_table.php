<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJoinAudioListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('join_audio_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('pos')->nullable();
            $table->string('shell_swiss_number', 24)->nullable();
            $table->string('join_audio_list_id', 24)->nullable();
            $table->string('join_audio_list_type', 24)->nullable();
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
        Schema::dropIfExists('join_audio_lists');
    }
}
