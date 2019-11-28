<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Fikrimi\Pipe\Enum\Repository;
use Fikrimi\Pipe\Models\Credential;
use Fikrimi\Pipe\Models\Project;
use Fikrimi\Pipe\Models\Stack;
use Illuminate\Foundation\Auth\User;
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

$factory->define(User::class, function (Faker $faker) {
    return [
        'name'              => $faker->name,
        'email'             => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token'    => Str::random(10),
    ];
});

$factory->define(Credential::class, function (Faker $faker) {
    return [
        'username' => $faker->userName,
        'type'     => $faker->randomElement([Credential::T_PASS, Credential::T_KEY]),
        'auth'     => $faker->text(100),
    ];
});

$factory->define(Stack::class, function (Faker $faker) {
    return [
        'name'        => $faker->domainName,
        'description' => $faker->text,
        'commands'    => [
            'echo "command 1"',
            'echo "command 2"',
        ],
    ];
});

$factory->define(Project::class, function (Faker $faker) {
    $domain = $faker->domainName;

    return [
        'name'          => $domain,
        'credential_id' => function () {
            return factory(Credential::class)->create()->id;
        },
        'repository'    => $faker->randomElement(Repository::all()),
        'host'          => $faker->ipv4,
        'dir_deploy'    => '/srv/www/' . Str::slug($domain) . '/deploy',
        'dir_workspace' => '/srv/www/' . Str::slug($domain) . '/workspace',
        'timeout'       => $faker->numberBetween(100, 1000),
        'branch'        => 'master',
        'commands'      => function () {
            return factory(Stack::class)->create()->commands;
        },
        'namespace'     => $faker->company . '/' . $domain,
    ];
});
