<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateTypeColumnToExcursionCarTimetable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('excursion_car_timetable', function (Blueprint $table) {
            $table->renameColumn('date', 'day');
        });

        Schema::table('excursion_car_timetable', function (Blueprint $table) {
            $table->string('day')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('excursion_car_timetable', function (Blueprint $table) {
            //
        });
    }
}
