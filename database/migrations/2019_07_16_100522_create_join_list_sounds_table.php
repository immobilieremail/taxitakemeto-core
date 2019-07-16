<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJoinListSoundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('join_list_sounds', function (Blueprint $table) {
            $table->bigIncrements('id_list');
            $table->bigInteger('id_sound');
            $table->timestamps();

            $table->foreign('id_list')
                ->references('id')
                ->on('sound_lists')
                ->onDelete('cascade');

            $table->foreign('id_sound')
                ->references('id')
                ->on('sounds')
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
        Schema::dropIfExists('join_list_sounds');
    }
}
