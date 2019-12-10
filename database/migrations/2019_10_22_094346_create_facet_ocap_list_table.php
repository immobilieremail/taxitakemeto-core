<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacetOcapListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facet_ocap_list', function (Blueprint $table) {
            $table->bigInteger('ocap_list_id');
            $table->string('facet_id');
            $table->timestamps();

            $table->foreign('ocap_list_id')
                  ->references('id')
                  ->on('ocap_lists')
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
        Schema::dropIfExists('facet_ocap_list');
    }
}
