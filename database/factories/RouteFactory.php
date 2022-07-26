<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Route;
use Faker\Generator as Faker;

$factory->define(Route::class, function (Faker $faker) {
    return [
        'name'          =>  $faker->name,
        'price_men'     =>  $faker->randomNumber(),
        'price_women'   =>  $faker->randomNumber(),
        'price_kids'   =>  $faker->randomNumber(),
        'price'   =>  $faker->randomNumber(),
        'prepayment'   =>  $faker->randomNumber(),
        'is_payable'   =>  $faker->boolean(),
        'color'   =>  $faker->hexColor,
    ];
});
