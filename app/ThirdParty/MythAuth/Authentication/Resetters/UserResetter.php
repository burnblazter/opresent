<?php

namespace App\ThirdParty\MythAuth\Authentication\Resetters;

use App\ThirdParty\MythAuth\Entities\User;

class UserResetter extends BaseResetter implements ResetterInterface
{
    /**
     * Sends reset message to the user via specified class
     * in `$activeResetter` setting in Config\Auth.php.
     *
     * @param User $user
     */
    public function send(?User $user = null): bool
    {
        if ($this->config->activeResetter === null) {
            return true;
        }

        $className = $this->config->activeResetter;

        $class = new $className();
        $class->setConfig($this->config);

        if ($class->send($user) === false) {
            log_message('error', lang('Auth.errorResetting', [$user->username]));
            $this->error = $class->error();

            return false;
        }

        return true;
    }
}
