<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacetShellUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facet_shell_user', function (Blueprint $table) {
            $table->bigInteger('shell_id');
            $table->string('facet_id');
            $table->timestamps();

            $table->foreign('shell_id')
                  ->references('id')
                  ->on('shells')
                  ->onDelete('cascade');

            $table->foreign('facet_id')
                  ->references('id')
                  ->on('facets')
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
        Schema::dropIfExists('facet_shell_user');
    }
}
