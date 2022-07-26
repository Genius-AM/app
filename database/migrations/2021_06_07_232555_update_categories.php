<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('categories')
            ->where('name', '=', 'Яхты')
            ->update(['name' => 'Море']);

        DB::table('categories')
            ->where('name', '=', 'Дайвинг')
            ->update(['name' => 'Сокровища Геленджика']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('categories')
            ->where('name', '=', 'Море')
            ->update(['name' => 'Яхты']);

        DB::table('categories')
            ->where('name', '=', 'Сокровища Геленджика')
            ->update(['name' => 'Дайвинг']);
    }
}
