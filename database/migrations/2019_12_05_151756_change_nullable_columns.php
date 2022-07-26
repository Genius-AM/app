<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNullableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
        });

        Schema::table('routes', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->default(null)->after('id');
            $table->unsignedBigInteger('subcategory_id')->nullable()->default(null)->change();

            $table->foreign('category_id')->references('id')->on('categories');
        });


        $routes = \App\Route::all();
        $subcategories = \App\Subcategory::pluck('id', 'category_id');
        foreach ($routes as $route) {
            $route->category_id = $subcategories[$route->subcategory_id];
            $route->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        Schema::table('routes', function (Blueprint $table) {
            $table->integer('subcategory_id')->change();
            $table->dropColumn('category_id');
        });
    }
}
