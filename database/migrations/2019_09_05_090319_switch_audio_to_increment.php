<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SwitchAudioToIncrement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audio', function (Blueprint $table) {
            $table->dropColumn('swiss_number');
            $table->dropColumn('audio_list_id');
            $table->dropColumn('path');
        });
        Schema::table('audio', function (Blueprint $table) {
            $table->string('path')->nullable();
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
        Schema::table('audio', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('path');
        });
        Schema::table('audio', function (Blueprint $table) {
            $table->string('path');
            $table->bigInteger('audio_list_id')->nullable();
            $table->string('swiss_number', 24)->primary();
        });
    }
}
