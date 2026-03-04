<?php
// \app\Config\App.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    /**
     * Default Base URL (Localhost)
     */
    public string $baseURL = 'http://localhost:8080/';

    /**
     * Allowed Hostnames
     */
    public array $allowedHostnames = [];

    /**
     * Index File
     */
    public string $indexPage = '';

    /**
     * URI Protocol
     */
    public string $uriProtocol = 'REQUEST_URI';

    /**
     * Default Locale
     */
    public string $defaultLocale = 'en';

    /**
     * Negotiate Locale
     */
    public bool $negotiateLocale = false;

    /**
     * Supported Locales
     */
    public array $supportedLocales = ['en'];

    /**
     * App Timezone
     */
    public string $appTimezone = 'Asia/Makassar';

    /**
     * Charset
     */
    public string $charset = 'UTF-8';

    /**
     * Force HTTPS
     */
    public bool $forceGlobalSecureRequests = false;

    /**
     * Session Driver
     */
    public string $sessionDriver = 'CodeIgniter\Session\Handlers\FileHandler';
    public string $sessionCookieName = 'ci_session';
    public int $sessionExpiration = 7200;
    public string $sessionSavePath = WRITEPATH . 'session';
    public bool $sessionMatchIP = false;
    public int $sessionTimeToUpdate = 300;
    public bool $sessionRegenerateDestroy = false;

    /**
     * Cookie Settings
     */
    public string $cookiePrefix = '';
    public string $cookieDomain = '';
    public string $cookiePath = '/';
    public bool $cookieSecure = false;
    public bool $cookieHTTPOnly = true;
    public ?string $cookieSameSite = 'Lax';

    /**
     * Proxy IPs
     */
    public array $proxyIPs = [
        '127.0.0.1' => 'X-Forwarded-For',
        '::1'       => 'X-Forwarded-For',
    ];

    /**
     * CSP
     */
    public bool $CSPEnabled = false;

    // --------------------------------------------------------------------
    // LOGIKA HYBRID (LOCALHOST + CLOUDFLARE)
    // --------------------------------------------------------------------
    public function __construct()
    {
        parent::__construct();

        // 1. Masukkan Domain Cloudflare Anda di sini!
        // Contoh: 'presensi.domainanda.com'
        $myCloudflareDomain = 'laragon.void.my.id'; 

        // Ambil hostname pengakses
        $currentHost = $_SERVER['HTTP_HOST'] ?? '';

        // JIKA yang akses pakai domain Cloudflare
        if ($currentHost === $myCloudflareDomain) {
            
            // Ubah Base URL jadi HTTPS Cloudflare
            $this->baseURL = 'https://' . $myCloudflareDomain . '/';
            
            // Masukkan ke whitelist allowed hostnames
            $this->allowedHostnames = [$myCloudflareDomain];

            // Trik agar CI4 generate link HTTPS (meski tunnel ke lokalnya HTTP)
            $_SERVER['HTTPS'] = 'on';
        }
        // JIKA akses localhost, biarkan settingan default (.env) yang bekerja
    }
}