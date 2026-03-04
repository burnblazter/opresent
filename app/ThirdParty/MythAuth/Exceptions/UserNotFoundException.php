<?php
// \app\ThirdParty\MythAuth\Exceptions\UserNotFoundException.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\ThirdParty\MythAuth\Exceptions;

use RuntimeException;

class UserNotFoundException extends RuntimeException implements ExceptionInterface
{
    public static function forUserID(int $id)
    {
        return new self(lang('Auth.userNotFound', [$id]), 404);
    }
}
