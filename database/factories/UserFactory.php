<?php

use App\Enums\UserStatus;
use App\Models\User;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(User::class, function (Faker $faker) {
    return [
        'spidCode' => Str::random(14),
        'name' => $faker->firstName,
        'familyName' => $faker->lastName,
        'fiscalNumber' => $faker->taxId(),
        'email' => $faker->unique()->safeEmail,
        'password_changed_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'status' => UserStatus::INACTIVE,
    ];
});

$factory->state(User::class, 'invited', [
    'status' => UserStatus::INVITED,
]);

$factory->state(User::class, 'pending', [
    'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
    'status' => UserStatus::PENDING,
]);

$factory->state(User::class, 'active', [
    'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
    'status' => UserStatus::ACTIVE,
]);

$factory->state(User::class, 'suspended', [
    'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
    'status' => UserStatus::SUSPENDED,
]);
]);
