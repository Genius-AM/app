<?php

use Illuminate\Database\Seeder;

class PackRouteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pack_route')->insert([
            ['route_id' => 1, 'pack_id' => 1,],
            ['route_id' => 3, 'pack_id' => 1,],
            ['route_id' => 17, 'pack_id' => 1,]
        ]);
    }
}