<?php
// \app\ThirdParty\MythAuth\Authentication\Resetters\EmailResetter.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\ThirdParty\MythAuth\Authentication\Resetters;

use Config\Email;
use App\ThirdParty\MythAuth\Entities\User;

/**
 * Class EmailResetter
 *
 * Sends a reset password email to user.
 */
class EmailResetter extends BaseResetter implements ResetterInterface
{
    /**
     * Sends a reset email
     *
     * @param User $user
     */
    public function send(?User $user = null): bool
    {
        $email  = service('email');
        $config = new Email();

        $settings = $this->getResetterSettings();

        $sent = $email->setFrom($settings->fromEmail ?? $config->fromEmail, $settings->fromName ?? $config->fromName)
            ->setTo($user->email)
            ->setSubject(lang('Auth.forgotSubject'))
            ->setMessage(view($this->config->views['emailForgot'], ['hash' => $user->reset_hash, 'user' => $user]))
            ->setMailType('html')
            ->send();

        if (! $sent) {
            $this->error = lang('Auth.errorEmailSent', [$user->email]);

            return false;
        }

        return true;
    }
}
