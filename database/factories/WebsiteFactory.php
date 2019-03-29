<?php

use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;
use App\Models\Website;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Website::class, function (Faker $faker) {
    return [
        'name' => $faker->words(5, true),
        'url' => $faker->domainName,
        'slug' => Str::slug($faker->domainName),
        'type' => WebsiteType::PRIMARY,
        'status' => WebsiteStatus::PENDING,
    ];
});

$factory->state(Website::class, 'active', [
    'status' => WebsiteStatus::ACTIVE,
]);

$factory->state(Website::class, 'archived', [
    'status' => WebsiteStatus::ARCHIVED,
]);
