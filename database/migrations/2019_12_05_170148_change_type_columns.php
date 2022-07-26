<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTypeColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
        });

        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['driver_id']);
                $table->dropForeign(['refuser_id']);
            });
        } catch (Exception $exception) {}

        Schema::table('roles', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('category_id')->nullable()->default(null)->change();
            $table->unsignedBigInteger('role_id')->change();
        });


        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
                $table->dropForeign(['role_id']);
            });
        } catch (Exception $exception) {}

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('role_id')->references('id')->on('roles');
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('driver_id')->change();
            $table->unsignedBigInteger('category_id')->nullable()->default(null)->change();

        });

        try {
            Schema::table('cars', function (Blueprint $table) {
                $table->dropForeign(['driver_id']);
            });
        } catch (Exception $exception) {}

        try {
            Schema::table('cars', function (Blueprint $table) {
                $table->foreign('driver_id')->references('id')->on('users');
            });
        } catch (Exception $exception) {}


        Schema::table('excursions', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('subcategory_id')->nullable()->default(null)->change();
            $table->unsignedBigInteger('route_id')->change();
            $table->unsignedBigInteger('car_id')->nullable()->change();
            $table->unsignedBigInteger('status_id')->nullable()->default(null)->change();
        });

        try {
            Schema::table('excursions', function (Blueprint $table) {
                $table->foreign('route_id')->references('id')->on('routes');
            });
        } catch (Exception $exception) {}


        Schema::table('orders', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('category_id')->nullable()->default(null)->change();
            $table->unsignedBigInteger('subcategory_id')->nullable()->default(null)->change();
            $table->unsignedBigInteger('route_id')->nullable()->default(null)->change();
            $table->unsignedBigInteger('client_id')->nullable()->default(null)->change();
            $table->unsignedBigInteger('manager_id')->nullable()->default(null)->change();
            $table->unsignedBigInteger('status_id')->change();
            $table->unsignedBigInteger('refuser_id')->nullable()->default(null)->change();
            $table->unsignedBigInteger('driver_id')->nullable()->default(null)->change();
        });

        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreign('driver_id')->references('id')->on('users');
                $table->foreign('refuser_id')->references('id')->on('users');
            });
        } catch (Exception $exception) {}


        Schema::table('excursion_order', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('excursion_id')->change();
            $table->unsignedBigInteger('order_id')->change();
        });

        try {
            Schema::table('excursion_order', function (Blueprint $table) {
                $table->foreign('excursion_id')->references('id')->on('excursions');
                $table->foreign('order_id')->references('id')->on('orders');
            });
        } catch (Exception $exception) {}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('routes', function (Blueprint $table) {
            //
        });
    }
}
