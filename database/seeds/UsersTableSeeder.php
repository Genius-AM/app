<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            ['name' => 'Иван Иванов', 'role_id' => 1, 'phone' => '+79991129757', 'balance' => 0, 'region' => 'Москва', 'address' => 'Вернадского 27 к.1', 'login' => 'manager', 'password' => Hash::make('ManagerPass2018@')],
            ['name' => 'Иван Иванов', 'role_id' => 2, 'phone' => '+79991129757', 'balance' => 0, 'region' => 'Москва', 'address' => 'Вернадского 27 к.1', 'login' => 'dispatcher', 'password' => Hash::make('DispatcherPass2018@')],
            ['name' => 'Тестовый Водитель', 'role_id' => 3, 'phone' => '+79955098474', 'balance' => 0, 'region' => 'Москва', 'address' => 'Вернадского 27 к.1', 'login' => 'driver', 'password' => Hash::make('DriverPass2018@')],
            ['name' => 'Админ Админов', 'role_id' => 4, 'phone' => '+79955098474', 'balance' => 0, 'region' => 'Москва', 'address' => 'Вернадского 27 к.1', 'login' => 'admin', 'password' => Hash::make('AdminPass2018@')],
        ]);

        DB::table('route_user')->insert([
            ['route_id' => 1, 'user_id' => 2],
            ['route_id' => 2, 'user_id' => 2],
        ]);
    }
}