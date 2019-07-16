<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJoinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('join_list_sound', function (Blueprint $table) {
            $table->bigInteger('id_list');
            $table->bigInteger('id_sound');
            $table->timestamps();

            $table->foreign('id_list')
                ->references('id')
                ->on('list');

            $table->foreign('id_sound')
                ->references('id')
                ->on('sound');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('join');
    }
}
