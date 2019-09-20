<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAudioListViewFacetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audio_list_view_facets', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->bigInteger('id_list')->unsigned();
            $table->string('id_shell');
            $table->timestamps();

            $table->foreign('id_list')
                ->references('id')
                ->on('audio_lists')
                ->onDelete('cascade');

            $table->foreign('id_shell')
                ->references('id')
                ->on('shells')
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
        Schema::dropIfExists('audio_list_view_facets');
    }
}
