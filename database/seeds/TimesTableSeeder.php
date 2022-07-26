<?php

use Illuminate\Database\Seeder;

class TimesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('times')->insert([
            ['name' => '8:00'],
            ['name' => '8:15'],
            ['name' => '8:30'],
            ['name' => '8:45'],
            ['name' => '9:00'],
            ['name' => '9:15'],
            ['name' => '9:30'],
            ['name' => '9:45'],
            ['name' => '10:00'],
            ['name' => '10:15'],
            ['name' => '10:30'],
            ['name' => '10:45'],
            ['name' => '11:00'],
            ['name' => '11:15'],
            ['name' => '11:30'],
            ['name' => '11:45'],
            ['name' => '12:00'],
            ['name' => '12:15'],
            ['name' => '12:30'],
            ['name' => '12:45'],
            ['name' => '13:00'],
            ['name' => '13:15'],
            ['name' => '13:30'],
            ['name' => '13:45'],
            ['name' => '14:00'],
            ['name' => '14:15'],
            ['name' => '14:30'],
            ['name' => '14:45'],
            ['name' => '15:00'],
            ['name' => '15:15'],
            ['name' => '15:30'],
            ['name' => '15:45'],
            ['name' => '16:00'],
            ['name' => '16:15'],
            ['name' => '16:30'],
            ['name' => '16:45'],
            ['name' => '17:00'],
            ['name' => '17:15'],
            ['name' => '17:30'],
            ['name' => '17:45'],
            ['name' => '18:00'],
            ['name' => '18:15'],
            ['name' => '18:30'],
            ['name' => '18:45'],
            ['name' => '19:00'],
            ['name' => '19:15'],
            ['name' => '19:30'],
            ['name' => '19:45'],
            ['name' => '19:00'],
            ['name' => '19:15'],
            ['name' => '19:30'],
            ['name' => '19:45'],
            ['name' => '20:00'],
        ]);
    }
}