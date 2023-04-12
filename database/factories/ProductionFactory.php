<?php

namespace App\Models\Production;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Generator as Faker;
use Illuminate\Support\Str;



$factory->define(Production::class, function (Faker $faker) {
    return [
        'idequipment' => $faker->randomDigitNotNull,
        'idproduct' => $faker->numberBetween($min = 282, $max = 510),
        'idpresentation' => $faker->randomDigitNotNull,
        'iddestination' => $faker->randomDigitNotNull,
        'idstatus' => $faker->numberBetween($min = 1, $max = 4),
        'productiongoal' => $faker->randomDigitNotNull,
        'productionorder' => $faker->word,
        'lot' => $faker->word,
        'idoperator' => $faker->randomDigitNotNull,
        'productiondate' => $faker->dateTime        
        
            ];
        });

