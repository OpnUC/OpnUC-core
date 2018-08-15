<?php

use Faker\Generator as Faker;

/* @var Illuminate\Database\Eloquent\Factory $factory */

$factory->define(App\SettingNumberRewrite::class, function (Faker $faker) {
    return [
        'pattern' => '	^(0\d+)$',
        'replacement' => '0\1',
        'description' => $faker->realText(10),
    ];
});
