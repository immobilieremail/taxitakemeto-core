<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SwitchMsgToSwiss extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shell_dropbox_messages', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('id_receiver');
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
        Schema::table('shell_dropbox_messages', function (Blueprint $table) {
            $table->dropColumn('swiss_number');
            $table->string('id_receiver', 24)->primary();
            $table->bigIncrements('id');
        });
    }
}
