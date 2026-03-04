<?php
// \app\Models\UsersRoleModel.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\Models;

use CodeIgniter\Model;

class UsersRoleModel extends Model
{
    protected $table = 'auth_groups_users';
    protected $allowedFields = ['group_id', 'user_id'];

    public function getUsersRole($user_id = false)
    {
        if ($user_id) {
            return $this->find($user_id);
        } else {
            return $this->findAll();
        }
    }
}
