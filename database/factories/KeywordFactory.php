<?php

use App\Models\Keyword;
use Faker\Generator as Faker;

$factory->define(Keyword::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'description' => $faker->text(50),
        'vocabulary' => $faker->word,
        'id_vocabulary' => $faker->word,
    ];
});
