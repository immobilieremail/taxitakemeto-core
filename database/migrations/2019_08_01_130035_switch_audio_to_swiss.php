<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SwitchAudioToSwiss extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('join_list_audio', function (Blueprint $table) {
            $table->dropForeign('join_list_audio_id_audio_foreign');
        });
        Schema::table('audio', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->string('swiss_number', 24)->primary();
        });
        Schema::table('join_list_audio', function (Blueprint $table) {
            $table->foreign('id_audio')
                ->references('swiss_number')
                ->on('audio')
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
        Schema::table('join_list_audio', function (Blueprint $table) {
            $table->dropForeign('join_list_audio_id_audio_foreign');
        });
        Schema::table('audio', function (Blueprint $table) {
            $table->string('id');
            $table->dropColumn('swiss_number');
        });
        Schema::table('join_list_audio', function (Blueprint $table) {
            $table->foreign('id_audio')
                ->references('id')
                ->on('audio')
                ->onDelete('cascade');
        });
    }
}
