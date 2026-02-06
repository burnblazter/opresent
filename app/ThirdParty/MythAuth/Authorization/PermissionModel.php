<?php

namespace App\ThirdParty\MythAuth\Authorization;

use App\ThirdParty\MythAuth\Models\PermissionModel as BaseModel;

/**
 * @deprecated 1.2.0 use App\ThirdParty\MythAuth\Models\PermissionModel instead
 */
class PermissionModel extends BaseModel
{
    protected $returnType = 'array';
}
