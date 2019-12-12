<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacetPITable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facet_p_i', function (Blueprint $table) {
            $table->bigInteger('p_i_id');
            $table->string('facet_id');
            $table->timestamps();

            $table->foreign('p_i_id')
                  ->references('id')
                  ->on('p_i_s')
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
        Schema::dropIfExists('facet_p_i');
    }
}
