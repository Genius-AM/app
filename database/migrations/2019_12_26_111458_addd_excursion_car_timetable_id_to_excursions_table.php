<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdddExcursionCarTimetableIdToExcursionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('excursions', function (Blueprint $table) {
            $table->unsignedBigInteger('exc_car_timetable_id')->default(null)->nullable()->after('id');

            $table->foreign('exc_car_timetable_id')->references('id')->on('excursion_car_timetable');
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
            //
        });
    }
}
