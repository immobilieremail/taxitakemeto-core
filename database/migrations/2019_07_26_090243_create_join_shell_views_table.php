<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJoinShellViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('join_shell_views', function (Blueprint $table) {
            $table->bigInteger('id_shell');
            $table->bigInteger('id_view');
            $table->timestamps();

            $table->foreign('id_shell')
                ->references('id')
                ->on('shells')
                ->onDelete('cascade');

            $table->foreign('id_view')
                ->references('id')
                ->on('ALViewFacets')
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
        Schema::dropIfExists('join_shell_views');
    }
}
