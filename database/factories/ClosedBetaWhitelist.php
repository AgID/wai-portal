<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\ClosedBetaWhitelist;
use Faker\Generator as Faker;

$factory->define(ClosedBetaWhitelist::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'payload' => null,
        'exception' => null,
    ];
});
