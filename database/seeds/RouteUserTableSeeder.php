<?php

use Illuminate\Database\Seeder;

class RouteUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('route_user')->insert([
            ['route_id' => 1, 'user_id' => 3],
            ['route_id' => 3, 'user_id' => 3],
            ['route_id' => 17, 'user_id' => 3],
        ]);
    }
}