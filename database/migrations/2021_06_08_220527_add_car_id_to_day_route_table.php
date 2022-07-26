<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCarIdToDayRouteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('day_route', function (Blueprint $table) {
            $table->unsignedBigInteger('car_id')->nullable()->after('route_id');

            $table->foreign('car_id')->references('id')->on('cars');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('day_route', function (Blueprint $table) {
            $table->dropForeign(['car_id']);
        });

        Schema::table('day_route', function (Blueprint $table) {
            $table->dropColumn('car_id');
        });
    }
}
