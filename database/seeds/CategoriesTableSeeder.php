<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            ['name' => 'Джиппинг',],
            ['name' => 'Сокровища Геленджика',],
            ['name' => 'Квадроциклы',],
            ['name' => 'Море',],
            ['name' => 'Автобусные экскурсии',],
            ['name' => 'Парашют',],
        ]);
    }
}
