<?php

namespace App\ThirdParty\MythAuth\Authorization;

use App\ThirdParty\MythAuth\Models\GroupModel as BaseModel;

/**
 * @deprecated 1.2.0 use App\ThirdParty\MythAuth\Models\GroupModel instead
 */
class GroupModel extends BaseModel
{
    protected $returnType             = 'object';
    protected string $permissionModel = PermissionModel::class;
}
