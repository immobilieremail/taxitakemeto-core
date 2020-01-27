<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacetTravelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facet_travel', function (Blueprint $table) {
            $table->bigInteger('travel_id');
            $table->string('facet_id');
            $table->timestamps();

            $table->foreign('travel_id')
                  ->references('id')
                  ->on('travels')
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
        Schema::dropIfExists('facet_travel');
    }
}
