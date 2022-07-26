<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDurationForRouteAndRouteCarTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->time('duration')->nullable()->default(null)->after('price');
        });

        Schema::table('route_car', function (Blueprint $table) {
            $table->time('duration')->nullable()->default(null)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('route', function (Blueprint $table) {
            //
        });
    }
}
