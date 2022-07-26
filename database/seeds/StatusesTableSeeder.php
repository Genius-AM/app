<?php

use Illuminate\Database\Seeder;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('statuses')->insert([
            ['name' => 'Принят'],
            ['name' => 'Формируется'],
            ['name' => 'Сформирован'],
            ['name' => 'Исполнен'],
            ['name' => 'Отказ'],
        ]);
    }
}