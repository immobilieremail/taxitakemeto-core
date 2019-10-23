<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAudioViewFacetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audio_view_facets', function (Blueprint $table) {
            $table->string('swiss_number', 24)->primary();
            $table->bigInteger('id_audio');
            $table->timestamps();

            $table->foreign('id_audio')
                ->references('id')
                ->on('audio')
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
        Schema::dropIfExists('audio_view_facets');
    }
}
