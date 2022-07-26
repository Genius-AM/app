<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExcursionCarTimetableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('excursion_car_timetable', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('car_id')->nullable();
            $table->unsignedBigInteger('route_id')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->boolean('self')->default(0);
            $table->timestamps();

            $table->foreign('car_id')->references('id')->on('cars');
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
        Schema::dropIfExists('excursion_car_timetable');
    }
}
