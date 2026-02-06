<?php

namespace App\ThirdParty\MythAuth\Test\Fakers;

use Faker\Generator;
use App\ThirdParty\MythAuth\Authorization\GroupModel;
use stdClass;

class GroupFaker extends GroupModel
{
    /**
     * Faked data for Fabricator.
     */
    public function fake(Generator &$faker): stdClass
    {
        return (object) [
            'name'        => $faker->word,
            'description' => $faker->sentence,
        ];
    }
}
