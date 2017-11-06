<?php

use Faker\Generator as Faker;

/* @var Illuminate\Database\Eloquent\Factory $factory */

$factory->define(App\AddressBookGroup::class, function (Faker $faker) {
    return [
        'parent_groupid' => 0,
        'type' => 1,
        'owner_userid' => 0,
        'group_name' => $faker->realText(10),
    ];
});
