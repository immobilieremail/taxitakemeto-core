<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJoinAudiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('join_audio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('pos')->nullable();
            $table->bigInteger('audio_list_id')->nullable();
            $table->string('join_audio_id', 24)->nullable();
            $table->string('join_audio_type', 24)->nullable();
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
        Schema::dropIfExists('join_audio');
    }
}
