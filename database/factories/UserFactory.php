<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'login' => $faker->regexify('[A-Za-z0-9]{10}'),
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => Str::random(10),
    ];
});

$faker = \Faker\Factory::create();

$factory->state(\App\User::class, 'admin', ['role_id' => \App\Role::ADMIN]);
$factory->state(\App\User::class, 'manager', ['role_id' => \App\Role::MANAGER]);
$factory->state(\App\User::class, 'dispatcher', ['role_id' => \App\Role::DISPATCHER]);
$factory->state(\App\User::class, 'driver', ['role_id' => \App\Role::DRIVER]);
$factory->state(\App\User::class, 'with_device', ['device_id' => $faker->uuid]);
