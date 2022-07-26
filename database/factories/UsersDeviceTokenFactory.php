<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\UsersDeviceToken;
use Faker\Generator as Faker;

$factory->define(UsersDeviceToken::class, function (Faker $faker) {
    return [
//        'user_id' => factory(\App\User::class)->state($faker->random()),
        'device_id' => $faker->uuid,
        'token' => $faker->regexify('[A-Za-z0-9]{152}'),
    ];
});

$factory->state(UsersDeviceToken::class, 'android', ['device_android' => 1]);
$factory->state(UsersDeviceToken::class, 'ios', ['device_ios' => 1]);