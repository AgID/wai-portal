<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Enums\PublicAdministrationStatus;
use App\Models\PublicAdministration;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(PublicAdministration::class, function (Faker $faker) {
    return [
        'ipa_code' => Str::random(5),
        'name' => $faker->company,
        'pec_address' => $faker->unique()->safeEmail,
        'city' => $faker->city,
        'county' => $faker->stateAbbr,
        'region' => $faker->state,
        'type' => 'secondary',
        'status' => PublicAdministrationStatus::PENDING,
    ];
});

$factory->state(PublicAdministration::class, 'active', [
    'status' => PublicAdministrationStatus::ACTIVE,
]);

$factory->state(PublicAdministration::class, 'suspended', [
    'status' => PublicAdministrationStatus::SUSPENDED,
]);
