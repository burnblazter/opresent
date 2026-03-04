<?php
// \app\ThirdParty\MythAuth\Commands\ActivateUser.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\ThirdParty\MythAuth\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\ThirdParty\MythAuth\Models\UserModel;

class ActivateUser extends BaseCommand
{
    protected $group       = 'Auth';
    protected $name        = 'auth:activate_user';
    protected $description = 'Activate Existing User.';
    protected $usage       = 'auth:activate_user [identity]';
    protected $arguments   = [
        'identity' => 'User identity.',
    ];

    public function run(array $params = [])
    {
        // Consume or prompt for password
        $identity = array_shift($params);

        if (empty($identity)) {
            $identity = CLI::prompt('Identity', null, 'required');
        }

        $type = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $userModel = new UserModel();
        $user      = $userModel->where($type, $identity)->first();

        if (! $user) {
            CLI::write('User with identity: ' . $identity . ' not found.', 'red');
        } else {
            $user->active = 1;

            if ($userModel->save($user)) {
                CLI::write('Sucessfuly activated the user with identity: ' . $identity, 'green');
            } else {
                CLI::write('Failed to activate the user with identity: ' . $identity, 'red');
            }
        }
    }
}
