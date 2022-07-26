<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePassengersAmountInExcursionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passengers_amount_in_excursion', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('category_id');
            $table->integer('subcategory_id');
            $table->integer('route_id');
            $table->string('date');
            $table->string('time');
            $table->integer('amount');

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
        Schema::dropIfExists('passengers_amount_in_excursion');
    }
}
