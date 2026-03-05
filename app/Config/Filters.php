<?php
// \app\Config\Filters.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    public array $aliases = [
        'csrf'          => CSRF::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'login'      => \App\ThirdParty\MythAuth\Filters\LoginFilter::class,
        'role'       => \App\ThirdParty\MythAuth\Filters\RoleFilter::class,
        'permission' => \App\ThirdParty\MythAuth\Filters\PermissionFilter::class,
    ];

    public array $globals = [
        'before' => [
            'login' => [
                'except' => [
                    'login', 
                    'login/*',
                    'register', 
                    'register/*',
                    'forgot', 
                    'forgot/*',
                    'reset-password', 
                    'reset-password/*',
                    'reset-feedback',
                    'reset-feedback/*',
                    'activate-account',
                    'activate-account/*',
                    'playground',
                    'playground/*',
                    'quote/random',
                ]
            ],
        ],
        'after' => [],
    ];

    public array $methods = [];
    public array $filters = [];
}