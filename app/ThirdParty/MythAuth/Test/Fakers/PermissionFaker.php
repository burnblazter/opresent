<?php
// \app\ThirdParty\MythAuth\Test\Fakers\PermissionFaker.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

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
