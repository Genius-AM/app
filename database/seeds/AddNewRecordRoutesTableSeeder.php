<?php

use Illuminate\Database\Seeder;

class AddNewRecordRoutesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('routes')->insert([
            ['subcategory_id' => 2, 'name' => 'Сокровища Геленджика', 'price_men' => 1300, 'price_women' => 1300, 'price_kids' => 1200, 'prepayment' => 200, 'is_payable' => true],
        ]);
    }
}
