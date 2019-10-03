<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SwitchShellToId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('join_audio_lists', function (Blueprint $table) {
            $table->dropColumn('shell_swiss_number');
            $table->bigInteger('shell_id')->nullable();
        });
        Schema::table('shells', function (Blueprint $table) {
            $table->dropColumn('swiss_number');
        });
        Schema::table('shells', function (Blueprint $table) {
            $table->bigIncrements('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('join_audio_lists', function (Blueprint $table) {
            $table->dropColumn('shell_id');
            $table->string('shell_swiss_number', 24)->nullable();
        });
        Schema::table('shells', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->string('swiss_number', 24)->primary();
        });
    }
}
