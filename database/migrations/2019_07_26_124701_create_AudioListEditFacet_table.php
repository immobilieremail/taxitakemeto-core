<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAudioListEditFacetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audio_list_edit_facets', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->bigInteger('id_shell');
            $table->bigInteger('id_list')->unsigned();
            $table->timestamps();

            $table->foreign('id_shell')
                ->references('id')
                ->on('shells')
                ->onDelete('cascade');

            $table->foreign('id_list')
                ->references('id')
                ->on('audio_lists')
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
        Schema::dropIfExists('audio_list_edit_facets');
    }
}
