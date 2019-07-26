<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJoinShellEditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('join_shell_edits', function (Blueprint $table) {
            $table->bigInteger('id_shell');
            $table->bigInteger('id_edit');
            $table->timestamps();

            $table->foreign('id_shell')
                ->references('id')
                ->on('shells')
                ->onDelete('cascade');

            $table->foreign('id_edit')
                ->references('id')
                ->on('audio_list_edit_facets')
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
        Schema::dropIfExists('join_shell_edits');
    }
}
