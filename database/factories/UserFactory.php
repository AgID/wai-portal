<?php

use App\Enums\UserStatus;
use App\Models\User;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

$factory->define(User::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\it_IT\Person($faker));

    return [
        'spidCode' => Str::random(14),
        'name' => $faker->firstName,
        'family_name' => $faker->lastName,
        'fiscal_number' => $faker->taxId(),
        'email' => $faker->unique()->safeEmail,
        'uuid' => Uuid::uuid4()->toString(),
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

$factory->state(User::class, 'password_expired', [
    'password_changed_at' => Carbon::now()->subDays(config('auth.password_expiry') + 1),
]);
