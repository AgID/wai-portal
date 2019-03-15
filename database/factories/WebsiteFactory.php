<?php

use App\Models\Website;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Website::class, function (Faker $faker) {
    return [
        'name' => $faker->words(5, true),
        'url' => $faker->domainName,
        'type' => 'primary',
        'slug' => Str::slug($faker->domainName),
        'status' => 'pending'
    ];
});

$factory->state(Website::class, 'active', [
    'status' => 'active'
]);

$factory->state(Website::class, 'suspended', [
    'status' => 'suspended'
]);
