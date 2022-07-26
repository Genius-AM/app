<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddErouteCarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_car', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('car_id')->nullable();
            $table->unsignedBigInteger('route_id')->nullable();
            $table->integer('price_men')->nullable();
            $table->integer('price_women')->nullable();
            $table->integer('price_kids')->nullable();
            $table->integer('price')->nullable();
            $table->integer('prepayment');
            $table->boolean('is_payable');
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
        Schema::dropIfExists('route_car');
    }
}
