<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarsAttachmentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars_attachment_histories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->bigIncrements('id');

            $table->integer('sort')->nullable()->comment("Номер сортировка");
            $table->integer('car_id')->unsigned()->comment("Id машины");
            $table->integer('driver_id')->unsigned()->nullable()->comment("Id водителя");
            $table->dateTime('begin_attach')->comment("Начало закрепления");
            $table->dateTime('end_attach')->nullable()->comment("Конец закрепления");

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
        Schema::dropIfExists('cars_attachment_histories');
    }
}
