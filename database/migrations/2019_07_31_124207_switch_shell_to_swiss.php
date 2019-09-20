<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SwitchShellToSwiss extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audio_list_view_facets', function (Blueprint $table) {
            $table->dropForeign('audio_list_view_facets_id_shell_foreign');
        });
        Schema::table('audio_list_edit_facets', function (Blueprint $table) {
            $table->dropForeign('audio_list_edit_facets_id_shell_foreign');
        });
        Schema::table('shells', function (Blueprint $table) {
            $table->string('swiss_number', 24)->primary();
            $table->dropColumn('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shells', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->dropColumn('swiss_number');
        });
        Schema::table('audio_list_view_facets', function (Blueprint $table) {
            $table->foreign('id_shell')
                    ->references('id')
                    ->on('shells')
                    ->onDelete('cascade');
        });
        Schema::table('audio_list_edit_facets', function (Blueprint $table) {
            $table->foreign('id_shell')
                    ->references('id')
                    ->on('shells')
                    ->onDelete('cascade');
        });
    }
}
