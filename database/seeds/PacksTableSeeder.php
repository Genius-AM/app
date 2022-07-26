<?php

use Illuminate\Database\Seeder;

class PacksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('packs')->insert([
            ['name' => 'Пакет 1', 'price' => 8000,]
        ]);
    }
}