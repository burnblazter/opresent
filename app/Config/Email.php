<?php
// app/Config/Email.php
/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

/*
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * SECURITY NOTICE (to whoever found hardcoded credentials in git history):
 * Those credentials have been rotated and are no longer valid.
 * Attempting to use them is pointless — and yes, we know you're looking.
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 */

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = '';
    public string $fromName   = '';
    public string $recipients = '';

    /**
     * The "user agent"
     */
    public string $userAgent = 'CodeIgniter';

    /**
     * The mail sending protocol: mail, sendmail, smtp
     */
    public string $protocol = 'smtp';

    /**
     * The server path to Sendmail.
     */
    public string $mailPath = '/usr/sbin/sendmail';

    /**
     * SMTP Server Hostname
     */
    public string $SMTPHost = '';

    /**
     * SMTP Username
     */
    public string $SMTPUser = '';

    /**
     * SMTP Password
     * WARNING: Never hardcode credentials here.
     * Use the .env file instead and ensure it is listed in .gitignore.
     */
    public string $SMTPPass = '';

    /**
     * SMTP Port
     * Common values: 25 (plain), 465 (SSL), 587 (TLS)
     */
    public int $SMTPPort = 465;

    /**
     * SMTP Timeout (in seconds)
     */
    public int $SMTPTimeout = 60;

    /**
     * Enable persistent SMTP connections
     */
    public bool $SMTPKeepAlive = false;

    /**
     * SMTP Encryption
     * Options: '' (none), 'tls', 'ssl'
     * Use 'ssl' for port 465, 'tls' for port 587
     */
    public string $SMTPCrypto = 'ssl';

    public function __construct()
    {
        parent::__construct();

        // Load from .env — values set here override class property defaults above.
        $this->fromEmail  = env('EMAIL_FROM_ADDRESS', $this->fromEmail);
        $this->fromName   = env('EMAIL_FROM_NAME', $this->fromName);
        $this->SMTPHost   = env('EMAIL_SMTP_HOST', $this->SMTPHost);
        $this->SMTPUser   = env('EMAIL_SMTP_USER', $this->SMTPUser);
        $this->SMTPPass   = env('EMAIL_SMTP_PASS', $this->SMTPPass);
        $this->SMTPPort   = (int) env('EMAIL_SMTP_PORT', $this->SMTPPort);
        $this->SMTPCrypto = env('EMAIL_SMTP_CRYPTO', $this->SMTPCrypto);
        $this->SMTPTimeout = (int) env('EMAIL_SMTP_TIMEOUT', $this->SMTPTimeout);
    }
}