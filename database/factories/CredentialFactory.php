<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Fikrimi\Pipe\Models\Credential;

$factory->define(Credential::class, function (Faker $faker) {
    return [
        'username' => $faker->userName,
        'type'     => $faker->randomElement([Credential::T_PASS, Credential::T_KEY]),
        'auth'     => $faker->text(100),
    ];
});
