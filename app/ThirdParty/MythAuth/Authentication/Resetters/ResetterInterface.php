<?php

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
