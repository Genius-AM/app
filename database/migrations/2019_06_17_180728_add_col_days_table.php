<?php

use App\Day;
use App\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('days', function (Blueprint $table) {
            $table->dropColumn('date');
            $table->string('weekday', 100)->after('name');

            $routes = Route::all();
            if(!empty( $routes )){
                foreach( $routes as $route ){
                    foreach($route->days as $day) {
                        $day->times()->detach();
                        Day::destroy($day->id);
                    }
                    $route->days()->detach();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('days', function (Blueprint $table) {
            $table->dropColumn('weekday');
            $table->date('date')->nullable()->after('name');
        });
    }
}
