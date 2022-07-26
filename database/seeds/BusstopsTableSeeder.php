<?php

use Illuminate\Database\Seeder;

class BusstopsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('busstops')->insert([
            ['name' => 'Остановка(1)',],
            ['name' => 'Остановка(2)',]
        ]);
    }
}
