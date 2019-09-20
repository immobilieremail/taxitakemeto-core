<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PolaizeAudiolistFacets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audio_list_edit_facets', function (Blueprint $table) {
            $table->dropColumn('id_shell');
            $table->dropColumn('id');
            $table->string('swiss_number', 24)->primary();
        });

        Schema::table('audio_list_view_facets', function (Blueprint $table) {
            $table->dropColumn('id_shell');
            $table->dropColumn('id');
            $table->string('swiss_number', 24)->primary();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audio_list_edit_facets', function (Blueprint $table) {
            $table->string('id_shell');
            $table->string('id')->primary();
            $table->dropColumn('swiss_number');
        });

        Schema::table('audio_list_view_facets', function (Blueprint $table) {
            $table->string('id_shell');
            $table->string('id')->primary();
            $table->dropColumn('swiss_number');
        });
    }
}
