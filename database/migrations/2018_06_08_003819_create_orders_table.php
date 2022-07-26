<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->increments('id');
            $table->tinyInteger('category_id');
            $table->integer('subcategory_id');
            $table->integer('route_id');
			$table->integer('client_id');
			$table->date('date');
			$table->time('time')->nullable();
			$table->integer('manager_id');
			$table->tinyInteger('men');
			$table->tinyInteger('women');
			$table->tinyInteger('kids');
            $table->string('address');
			$table->integer('price');
			$table->integer('prepayment');
			$table->tinyInteger('status_id');
            $table->boolean('is_pack');
            $table->integer('pack_id')->nullable();
			$table->boolean('pack_created');
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
        Schema::dropIfExists('orders');
    }
}
