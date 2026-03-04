<?php
// \app\ThirdParty\MythAuth\Config\Registrar.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\ThirdParty\MythAuth\Config;

/**
 * Helper class that will register our bulk plugins
 * and filters with the View Parser class.
 *
 * Called automatically by Config\View as long as
 * this file is setup as a Registrar:
 *
 *      protected $registrars = [
 *          \Myth\Template\Registrar::class
 *      ];
 */
class Registrar
{
    public static function View()
    {
        return [
            'plugins' => [
                'logged_in'  => [static fn ($str, array $params = []) => service('authentication')->check() ? $str : ''],
                'logged_out' => [static fn ($str, array $params = []) => ! service('authentication')->check() ? $str : ''],
            ],
        ];
    }
}
