<?php
// \app\ThirdParty\MythAuth\Commands\ListGroups.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\ThirdParty\MythAuth\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ListGroups extends BaseCommand
{
    protected $group       = 'Auth';
    protected $name        = 'auth:list_groups';
    protected $description = 'Lists groups from the database.';
    protected $usage       = 'auth:list_groups';

    public function run(array $params)
    {
        $db = db_connect();

        // get all groups
        $rows = $db->table('auth_groups')
            ->select('id, name, description')
            ->orderBy('name', 'asc')
            ->get()->getResultArray();

        if (empty($rows)) {
            CLI::write(CLI::color('There are no groups.', 'yellow'));
        } else {
            $thead = ['Group ID', 'Name', 'Description'];
            CLI::table($rows, $thead);
        }
    }
}
