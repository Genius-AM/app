<?php

use Illuminate\Database\Seeder;

class TimesSecondTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('times')->insert([
            ['name' => '20:15'],
            ['name' => '20:30'],
            ['name' => '20:45'],
            ['name' => '21:00'],
        ]);
    }
}