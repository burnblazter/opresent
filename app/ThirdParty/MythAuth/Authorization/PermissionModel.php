<?php
// \app\ThirdParty\MythAuth\Authorization\PermissionModel.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\ThirdParty\MythAuth\Authorization;

use App\ThirdParty\MythAuth\Models\PermissionModel as BaseModel;

/**
 * @deprecated 1.2.0 use App\ThirdParty\MythAuth\Models\PermissionModel instead
 */
class PermissionModel extends BaseModel
{
    protected $returnType = 'array';
}
