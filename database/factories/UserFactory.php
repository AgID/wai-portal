<?php

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(User::class, function (Faker $faker) {
    return [
        'spidCode' => Str::random(14),
        'name' => $faker->firstName,
        'familyName' => $faker->lastName,
        'fiscalNumber' => $faker->taxId(),
        'email' => $faker->unique()->safeEmail,
        'status' => 'inactive',
    ];
});

$factory->state(User::class, 'pending', [
    'status' => 'pending',
]);

$factory->state(User::class, 'active', [
    'status' => 'active',
]);

$factory->state(User::class, 'suspended', [
    'status' => 'suspended',
]);
