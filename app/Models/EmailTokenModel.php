<?php
// \app\Models\EmailTokenModel.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\Models;

use CodeIgniter\Model;

class EmailTokenModel extends Model
{
    protected $table = 'email_tokens';
    protected $primaryKey = 'id';
    protected $allowedFields = ['email', 'token', 'created_time'];
    protected $useTimestamps = true;
}
