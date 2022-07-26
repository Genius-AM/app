<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBookedToExcursionCarTimetableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('excursion_car_timetable', function (Blueprint $table) {
            $table->boolean('booked')->default(false)->after('self');
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
            $table->dropColumn('booked');
        });
    }
}
