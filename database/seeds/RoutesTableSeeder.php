<?php

use Illuminate\Database\Seeder;

class RoutesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('routes')->insert([
            ['subcategory_id' => 1, 'name' => 'Грозовые ворота', 'price_men' => 1300, 'price_women' => 1300, 'price_kids' => 1200, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 1, 'name' => 'Долина водопадов', 'price_men' => 1600, 'price_women' => 1600, 'price_kids' => 1500, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 1, 'name' => 'Горы кавказа', 'price_men' => 1900, 'price_women' => 1900, 'price_kids' => 1800, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 2, 'name' => 'Тонкий мыс', 'price_men' => 2000, 'price_women' => 2000, 'price_kids' => 2000, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 2, 'name' => 'Приморье', 'price_men' => 2000, 'price_women' => 2000, 'price_kids' => 2000, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 3, 'name' => 'Церковное озеро', 'price_men' => 2500, 'price_women' => 2500, 'price_kids' => 3600, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 3, 'name' => 'Сафари к озеру желаний', 'price_men' => 2500, 'price_women' => 2500, 'price_kids' => 3600, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 3, 'name' => 'Голубая бездна', 'price_men' => 2800, 'price_women' => 2800, 'price_kids' => 4200, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 4, 'name' => 'Аврора', 'price_men' => 700, 'price_women' => 700, 'price_kids' => 700, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 4, 'name' => 'Фараон', 'price_men' => 700, 'price_women' => 700, 'price_kids' => 700, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 4, 'name' => 'Ассоль', 'price_men' => 700, 'price_women' => 700, 'price_kids' => 700, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 5, 'name' => 'Аврора', 'price_men' => 900, 'price_women' => 900, 'price_kids' => 900, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 5, 'name' => 'Фараон', 'price_men' => 900, 'price_women' => 900, 'price_kids' => 900, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 5, 'name' => 'Ассоль', 'price_men' => 900, 'price_women' => 900, 'price_kids' => 900, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 6, 'name' => 'Яхта', 'price_men' => 3000, 'price_women' => 3000, 'price_kids' => 3000, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 6, 'name' => 'Катер', 'price_men' => 6000, 'price_women' => 6000, 'price_kids' => 6000, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 7, 'name' => 'Винзавод', 'price_men' => 500, 'price_women' => 500, 'price_kids' => 500, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 7, 'name' => 'Сокровища Геленджика', 'price_men' => 1600, 'price_women' => 1600, 'price_kids' => 1500, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 7, 'name' => 'Скала Парус', 'price_men' => 800, 'price_women' => 800, 'price_kids' => 700, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 8, 'name' => 'Крымский мост', 'price_men' => 1200, 'price_women' => 1200, 'price_kids' => 1100, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 8, 'name' => 'Азовское море', 'price_men' => 1200, 'price_women' => 1200, 'price_kids' => 1100, 'prepayment' => 200, 'is_payable' => true],
            ['subcategory_id' => 9, 'name' => 'Прыжок', 'price_men' => 3500, 'price_women' => 3500, 'price_kids' => 4500, 'prepayment' => 200, 'is_payable' => true],
        ]);
    }
}