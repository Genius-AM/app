<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAmountDetailedToPassengersAmountInExcursionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('passengers_amount_in_excursion', function (Blueprint $table) {
            $table->integer('amount_kids')->nullable()->default(null)->after('amount');
            $table->integer('amount_women')->nullable()->default(null)->after('amount');
            $table->integer('amount_men')->nullable()->default(null)->after('amount');
            $table->integer('amount')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('passengers_amount_in_excursion', function (Blueprint $table) {
            //
        });
    }
}
