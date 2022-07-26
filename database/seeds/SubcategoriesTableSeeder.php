<?php

use Illuminate\Database\Seeder;

class SubcategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('subcategories')->insert([
            ['category_id' => 1, 'name' => 'Все'],
            ['category_id' => 2, 'name' => 'Все'],
            ['category_id' => 3, 'name' => 'Все'],
            ['category_id' => 4, 'name' => 'Прогулка'],
            ['category_id' => 4, 'name' => 'Рыбалка'],
            ['category_id' => 4, 'name' => 'Аренда'],
            ['category_id' => 5, 'name' => 'Лесные угодья',],
            ['category_id' => 5, 'name' => 'Меридиан',],
            ['category_id' => 6, 'name' => 'Все'],
        ]);
    }
}
