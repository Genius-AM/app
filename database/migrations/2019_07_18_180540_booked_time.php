<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BookedTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booked_time', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->increments('id');

            $table->integer('category_id')->comment("Ид категории");
            $table->integer('subcategory_id')->comment("Ид категории");
            $table->integer('route_id')->comment("Ид маршрута");
            $table->string('date')->comment("Дата");
            $table->string('time')->nullable()->comment("Время");
            $table->boolean('booked')->comment("Бронь");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booked_time');
    }
}
