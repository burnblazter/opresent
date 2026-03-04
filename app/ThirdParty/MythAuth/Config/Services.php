<?php
// \app\ThirdParty\MythAuth\Config\Services.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\ThirdParty\MythAuth\Config;

use CodeIgniter\Model;
use Config\Services as BaseService;
use App\ThirdParty\MythAuth\Authentication\Activators\ActivatorInterface;
use App\ThirdParty\MythAuth\Authentication\Activators\UserActivator;
use App\ThirdParty\MythAuth\Authentication\Passwords\PasswordValidator;
use App\ThirdParty\MythAuth\Authentication\Resetters\EmailResetter;
use App\ThirdParty\MythAuth\Authentication\Resetters\ResetterInterface;
use App\ThirdParty\MythAuth\Authorization\FlatAuthorization;
use App\ThirdParty\MythAuth\Authorization\GroupModel;
use App\ThirdParty\MythAuth\Authorization\PermissionModel;
use App\ThirdParty\MythAuth\Config\Auth as AuthConfig;
use App\ThirdParty\MythAuth\Models\LoginModel;
use App\ThirdParty\MythAuth\Models\UserModel;

class Services extends BaseService
{
    public static function authentication(string $lib = 'local', ?Model $userModel = null, ?Model $loginModel = null, bool $getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('authentication', $lib, $userModel, $loginModel);
        }

        $userModel ??= model(UserModel::class);
        $loginModel ??= model(LoginModel::class);

        /** @var AuthConfig $config */
        $config   = config('Auth');
        $class    = $config->authenticationLibs[$lib];
        $instance = new $class($config);

        return $instance
            ->setUserModel($userModel)
            ->setLoginModel($loginModel);
    }

    // Note that these input models *must be* of types GroupModel, PermissionModel, and UserModel respectively
    public static function authorization(?Model $groupModel = null, ?Model $permissionModel = null, ?Model $userModel = null, bool $getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('authorization', $groupModel, $permissionModel, $userModel);
        }

        $groupModel ??= model(GroupModel::class);
        $permissionModel ??= model(PermissionModel::class);
        $userModel ??= model(UserModel::class);

        $instance = new FlatAuthorization($groupModel, $permissionModel); // @phpstan-ignore-line

        return $instance->setUserModel($userModel); // @phpstan-ignore-line
    }

    /**
     * Returns an instance of the PasswordValidator.
     */
    public static function passwords(?AuthConfig $config = null, bool $getShared = true): PasswordValidator
    {
        if ($getShared) {
            return self::getSharedInstance('passwords', $config);
        }

        return new PasswordValidator($config ?? config(AuthConfig::class));
    }

    /**
     * Returns an instance of the Activator.
     */
    public static function activator(?AuthConfig $config = null, bool $getShared = true): ActivatorInterface
    {
        if ($getShared) {
            return self::getSharedInstance('activator', $config);
        }

        $config ??= config(AuthConfig::class);
        $class = $config->requireActivation ?? UserActivator::class;

        /** @var class-string<ActivatorInterface> $class */
        return new $class($config);
    }

    /**
     * Returns an instance of the Resetter.
     */
    public static function resetter(?AuthConfig $config = null, bool $getShared = true): ResetterInterface
    {
        if ($getShared) {
            return self::getSharedInstance('resetter', $config);
        }

        $config ??= config(AuthConfig::class);
        $class = $config->activeResetter ?? EmailResetter::class;

        /** @var class-string<ResetterInterface> $class */
        return new $class($config);
    }
}
