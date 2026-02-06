<?php

namespace App\ThirdParty\MythAuth\Test\Fakers;

use Faker\Generator;
use App\ThirdParty\MythAuth\Authorization\PermissionModel;

class PermissionFaker extends PermissionModel
{
    /**
     * Faked data for Fabricator.
     */
    public function fake(Generator &$faker): array
    {
        return [
            'name'        => $faker->word,
            'description' => $faker->sentence,
        ];
    }
}
