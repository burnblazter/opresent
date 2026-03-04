<?php
// \app\ThirdParty\MythAuth\Filters\BaseFilter.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\ThirdParty\MythAuth\Filters;

use App\ThirdParty\MythAuth\Config\Auth as AuthConfig;

abstract class BaseFilter
{
    /**
     * Landing Route
     */
    protected $landingRoute;

    /**
     * Reserved Routes
     */
    protected $reservedRoutes;

    /**
     * Authenticate
     */
    protected $authenticate;

    /**
     * Authorize
     */
    protected $authorize;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Load the Auth config, for constructor only!!!
        $config = config(AuthConfig::class);

        // Load the routes
        $this->landingRoute   = $config->landingRoute;
        $this->reservedRoutes = $config->reservedRoutes;

        // Load the authenticate service
        $this->authenticate = service('authentication');

        // Load the authorize service
        $this->authorize = service('authorization');

        // Load the helper
        if (! function_exists('logged_in')) {
            helper('auth');
        }
    }
}
