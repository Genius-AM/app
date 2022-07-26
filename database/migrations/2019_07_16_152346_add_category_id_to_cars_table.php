<?php

use App\Cars;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryIdToCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->integer('category_id')
                ->after('car_number');
        });

        $our_cars = Cars::where('owner','our')->get();
        foreach ($our_cars as $key=>$value){
            $current_car = Cars::find($value->id);
            $current_car->category_id=1;
            $current_car->passengers_amount=8;
            $current_car->save();
        }

        $partner_cars = Cars::where('owner','partner')->get();
        foreach ($partner_cars as $key=>$value){
            $current_car = Cars::find($value->id);
            $current_car->category_id=1;
            $current_car->passengers_amount=9999;
            $current_car->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
    }
}
