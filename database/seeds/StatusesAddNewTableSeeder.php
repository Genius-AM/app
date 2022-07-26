<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusesAddNewTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('statuses')->insert([
            ['name' => 'Удалена'],
        ]);
    }
}
