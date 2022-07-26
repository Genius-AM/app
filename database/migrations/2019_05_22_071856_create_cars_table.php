<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->increments('id');

            $table->string('name', 30)->comment("Название машины");
            $table->string('car_number', 30)->comment("Гос номер");
            $table->smallInteger('passengers_amount')->comment("Количество пассажиров");
            $table->string('owner', 30)->comment("Чья машина (наша или партнеров)");
            $table->string('owner_name', 100)->nullable()->comment("Если машина партнеров, то здесь будет имя партнера");
            $table->integer('driver_id')->nullable()->comment("Id водителя");
            $table->integer('order')->default(0)->comment("Номер сортировки");

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
        Schema::dropIfExists('cars');
    }
}
