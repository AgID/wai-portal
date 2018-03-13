<?php

use App\Models\PublicAdministration;
use Faker\Generator as Faker;

$factory->define(PublicAdministration::class, function (Faker $faker) {
    return [
        'ipa_code' => str_random(5),
        'name' => $faker->company,
        'pec_address' => $faker->unique()->safeEmail,
        'status' => 'pending'
    ];
});

$factory->state(PublicAdministration::class, 'active', [
    'status' => 'active'
]);

$factory->state(PublicAdministration::class, 'suspended', [
    'status' => 'suspended'
]);
