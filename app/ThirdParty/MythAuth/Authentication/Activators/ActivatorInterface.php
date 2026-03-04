<?php
// \app\ThirdParty\MythAuth\Authentication\Activators\ActivatorInterface.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\ThirdParty\MythAuth\Authentication\Activators;

use App\ThirdParty\MythAuth\Entities\User;

/**
 * Interface ActivatorInterface
 */
interface ActivatorInterface
{
    /**
     * Send activation message to user
     *
     * @param User $user
     */
    public function send(?User $user = null): bool;

    /**
     * Returns the error string that should be displayed to the user.
     */
    public function error(): string;
}
