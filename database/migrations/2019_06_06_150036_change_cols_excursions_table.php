<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColsExcursionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('excursions', function (Blueprint $table) {
            $table->dropColumn('driver_id');
            $table->integer('car_id')->after('route_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('excursions', function (Blueprint $table) {
            $table->dropColumn('car_id');
            $table->integer('driver_id')->after('route_id');
        });
    }
}
