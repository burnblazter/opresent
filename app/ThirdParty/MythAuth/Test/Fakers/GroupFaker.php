<?php
// \app\ThirdParty\MythAuth\Test\Fakers\GroupFaker.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

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
