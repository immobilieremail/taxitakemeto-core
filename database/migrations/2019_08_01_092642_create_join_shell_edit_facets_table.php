<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJoinShellEditFacetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('join_shell_edit_facets', function (Blueprint $table) {
            $table->string('id_shell', 24);
            $table->string('id_facet', 24);
            $table->timestamps();

            $table->foreign('id_shell')
                ->references('swiss_number')
                ->on('shells')
                ->onDelete('cascade');

            $table->foreign('id_facet')
                ->references('swiss_number')
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
        Schema::dropIfExists('join_shell_edit_facets');
    }
}
