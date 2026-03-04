<?php
// \app\ThirdParty\MythAuth\Authentication\Resetters\ResetterInterface.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\ThirdParty\MythAuth\Authentication\Resetters;

use App\ThirdParty\MythAuth\Entities\User;

/**
 * Interface ResetterInterface
 */
interface ResetterInterface
{
    /**
     * Send reset message to user
     *
     * @param User $user
     */
    public function send(?User $user = null): bool;

    /**
     * Returns the error string that should be displayed to the user.
     */
    public function error(): string;
}
