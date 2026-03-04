<?php
// \app\ThirdParty\MythAuth\Authentication\Activators\UserActivator.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\ThirdParty\MythAuth\Authentication\Activators;

use App\ThirdParty\MythAuth\Entities\User;

class UserActivator extends BaseActivator implements ActivatorInterface
{
    /**
     * Sends activation message to the user via specified class
     * in `$requireActivation` setting in Config\Auth.php.
     *
     * @param User $user
     */
    public function send(?User $user = null): bool
    {
        if (! $this->config->requireActivation) {
            return true;
        }

        $className = $this->config->requireActivation;

        $class = new $className();
        $class->setConfig($this->config);

        if ($class->send($user) === false) {
            log_message('error', "Failed to send activation message to: {$user->email}");
            $this->error = $class->error();

            return false;
        }

        return true;
    }
}
