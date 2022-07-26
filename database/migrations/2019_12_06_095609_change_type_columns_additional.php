<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTypeColumnsAdditional extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_device_token', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        Schema::table('booked_time', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('route_id')->change();
        });

        Schema::table('booked_time', function (Blueprint $table) {
            $table->foreign('route_id')->references('id')->on('routes');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_device_token', function (Blueprint $table) {
            //
        });
    }
}
