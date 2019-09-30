<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;
use App\Models\Website;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Website::class, function (Faker $faker) {
    $domain_name = $faker->domainName;

    return [
        'name' => $faker->words(5, true),
        'url' => $domain_name,
        'type' => WebsiteType::PRIMARY,
        'slug' => Str::slug($domain_name),
        'analytics_id' => rand(),
        'status' => WebsiteStatus::PENDING,
    ];
});

$factory->state(Website::class, 'active', [
    'status' => WebsiteStatus::ACTIVE,
]);

$factory->state(Website::class, 'archived', [
    'status' => WebsiteStatus::ARCHIVED,
]);
