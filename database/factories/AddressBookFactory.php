<?php

use Faker\Generator as Faker;

/* @var Illuminate\Database\Eloquent\Factory $factory */

$factory->define(App\AddressBook::class, function (Faker $faker) {
    return [
        'type' => 1,
        'owner_userid' => 0,
        'position' => $faker->realText(10),
        'name_kana' => $faker->name,
        'name' => $faker->name,
        'tel1' => rand(300,399),
        'email' => $faker->email,
        'comment' => $faker->realText(20),
    ];
});
