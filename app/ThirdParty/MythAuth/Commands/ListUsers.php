<?php
// \app\ThirdParty\MythAuth\Commands\ListUsers.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\ThirdParty\MythAuth\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ListUsers extends BaseCommand
{
    protected $group       = 'Auth';
    protected $name        = 'auth:list_users';
    protected $description = 'Lists users from the database.';
    protected $usage       = 'auth:list_users';

    public function run(array $params)
    {
        $db = db_connect();

        // get all groups
        $rows = $db->table('users')
            ->select('id, username, email')
            ->orderBy('id', 'asc')
            ->get()->getResultArray();

        if (empty($rows)) {
            CLI::write(CLI::color('There are no users.', 'yellow'));
        } else {
            $thead = ['User ID', 'Username', 'E-Mail'];
            CLI::table($rows, $thead);
        }
    }
}
