<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompanyIdToPassengersAmount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('passengers_amount_in_excursion', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->default(null)->nullable()->after('route_id');
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
