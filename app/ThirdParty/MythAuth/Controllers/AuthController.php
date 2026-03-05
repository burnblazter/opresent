<?php
// \app\ThirdParty\MythAuth\Controllers\AuthController.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\ThirdParty\MythAuth\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Session\Session;
use App\ThirdParty\MythAuth\Config\Auth as AuthConfig;
use App\ThirdParty\MythAuth\Entities\User;
use App\ThirdParty\MythAuth\Models\UserModel;

class AuthController extends Controller
{
    protected $auth;

    /**
     * @var AuthConfig
     */
    protected $config;

    /**
     * @var Session
     */
    protected $session;

    public function __construct()
    {
        // Most services in this controller require
        // the session to be started - so fire it up!
        $this->session = service('session');

        $this->config = config('Auth');
        $this->auth   = service('authentication');
    }

    //--------------------------------------------------------------------
    // Login/out
    //--------------------------------------------------------------------

    /**
     * Displays the login form, or redirects
     * the user to their destination/home if
     * they are already logged in.
     */
    public function login()
    {
        // No need to show a login form if the user
        // is already logged in.
        if ($this->auth->check()) {
            $redirectURL = session('redirect_url') ?? site_url('/');
            unset($_SESSION['redirect_url']);

            return redirect()->to($redirectURL);
        }

        // Set a return URL if none is specified
        $_SESSION['redirect_url'] = session('redirect_url') ?? previous_url() ?? site_url('/');

        return $this->_render($this->config->views['login'], ['config' => $this->config]);
    }

    /**
     * Attempts to verify the user's credentials
     * through a POST request.
     */
    public function attemptLogin()
    {
        $rules = [
            'login'    => 'required',
            'password' => 'required',
        ];
        if ($this->config->validFields === ['email']) {
            $rules['login'] .= '|valid_email';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $login    = $this->request->getPost('login');
        $password = $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');

        // Determine credential type
        $type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Try to log them in...
        if (! $this->auth->attempt([$type => $login, 'password' => $password], $remember)) {
            return redirect()->back()->withInput()->with('error', $this->auth->error() ?? lang('Auth.badAttempt'));
        }

        // Is the user being forced to reset their password?
        if ($this->auth->user()->force_pass_reset === true) {
            return redirect()->to(route_to('reset-password') . '?token=' . $this->auth->user()->reset_hash . '&email=' . urlencode($this->auth->user()->email))->withCookies();
        }

        $redirectURL = session('redirect_url') ?? site_url('/');
        unset($_SESSION['redirect_url']);

        // If the redirect target is the default home, apply group-based overrides
        $user = $this->auth->user();
        if ($redirectURL === site_url('/')) {
            helper('auth'); // ensure in_groups() is available

            if (function_exists('in_groups')) {
                if (in_groups('kiosk')) {
                    return redirect()->to(base_url('kiosk'))->withCookies()->with('message', lang('Auth.loginSuccess'));
                }
                if (in_groups('helper')) {
                    return redirect()->to(base_url('data-pegawai'))->withCookies()->with('message', lang('Auth.loginSuccess'));
                }
            }

            // Fallback: check on the user entity if available
            if ($user) {
                try {
                    if (method_exists($user, 'inGroup') && $user->inGroup('kiosk')) {
                        return redirect()->to(base_url('kiosk'))->withCookies()->with('message', lang('Auth.loginSuccess'));
                    }
                    if (method_exists($user, 'inGroup') && $user->inGroup('helper')) {
                        return redirect()->to(base_url('data-pegawai'))->withCookies()->with('message', lang('Auth.loginSuccess'));
                    }
                } catch (\Throwable $e) {
                    // ignore and fall back to default redirect
                }
            }
        }

        return redirect()->to($redirectURL)->withCookies()->with('message', lang('Auth.loginSuccess'));
    }

    /**
     * Log the user out.
     */
    public function logout()
    {
        if ($this->auth->check()) {
            $this->auth->logout();
        }

        return redirect()->to(site_url('/'));
    }

    //--------------------------------------------------------------------
    // Register
    //--------------------------------------------------------------------

    /**
     * Displays the user registration page.
     */
    public function register()
    {
        // check if already logged in.
        if ($this->auth->check()) {
            return redirect()->back();
        }

        // Check if registration is allowed
        if (! $this->config->allowRegistration) {
            return redirect()->back()->withInput()->with('error', lang('Auth.registerDisabled'));
        }

        return $this->_render($this->config->views['register'], ['config' => $this->config]);
    }

    /**
     * Attempt to register a new user.
     */
    public function attemptRegister()
    {
        // Check if registration is allowed
        if (! $this->config->allowRegistration) {
            return redirect()->back()->withInput()->with('error', lang('Auth.registerDisabled'));
        }

        $users = model(UserModel::class);

        // Validate basics first since some password rules rely on these fields
        $rules = config('Validation')->registrationRules ?? [
            'username' => 'required|alpha_numeric_space|min_length[3]|max_length[30]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validate passwords since they can only be validated properly here
        $rules = [
            'password'     => 'required|strong_password',
            'pass_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Save the user
        $allowedPostFields = array_merge(['password'], $this->config->validFields, $this->config->personalFields);
        $user              = new User($this->request->getPost($allowedPostFields));

        $this->config->requireActivation === null ? $user->activate() : $user->generateActivateHash();

        // Ensure default group gets assigned if set
        if (! empty($this->config->defaultUserGroup)) {
            $users = $users->withGroup($this->config->defaultUserGroup);
        }

        if (! $users->save($user)) {
            return redirect()->back()->withInput()->with('errors', $users->errors());
        }

        if ($this->config->requireActivation !== null) {
            $activator = service('activator');
            $sent      = $activator->send($user);

            if (! $sent) {
                return redirect()->back()->withInput()->with('error', $activator->error() ?? lang('Auth.unknownError'));
            }

            // Success!
            return redirect()->route('login')->with('message', lang('Auth.activationSuccess'));
        }

        // Success!
        return redirect()->route('login')->with('message', lang('Auth.registerSuccess'));
    }

    //--------------------------------------------------------------------
    // Forgot Password
    //--------------------------------------------------------------------

    /**
     * Displays the forgot password form.
     */
    public function forgotPassword()
    {
        if ($this->config->activeResetter === null) {
            return redirect()->route('login')->with('error', lang('Auth.forgotDisabled'));
        }

        return $this->_render($this->config->views['forgot'], ['config' => $this->config]);
    }

    /**
     * Attempts to find a user account with that password
     * and send password reset instructions to them.
     */
    public function attemptForgot($email = false)
    {
    if ($this->config->activeResetter === null) {
        return redirect()->route('login')->with('error', lang('Auth.forgotDisabled'));
    }

    if (!$email) {
        $rules = [
            'email' => [
                'label' => lang('Auth.emailAddress'),
                'rules' => 'required|valid_email',
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
    }

    $users = model(UserModel::class);

    if ($email) {
        $user = $users->where('email', $email)->first();
    } else {
        $user = $users->where('email', $this->request->getPost('email'))->first();
    }

    if (null === $user) {
        return redirect()->back()->with('error', lang('Auth.forgotNoUser'));
    }

    // Save the reset hash /
    $user->generateResetHash();
    $users->save($user);

    $resetter = service('resetter');
    $sent     = $resetter->send($user);

    if (!$sent) {
        $adminEmail = config('Email')->fromEmail ?? 'admin@presensi.local';
        return redirect()->back()->withInput()
            ->with('error', 'Email gagal dikirim. Hubungi admin: ' . $adminEmail);
    }

    // Redirect ke halaman feedback dengan email di query parameter
    $emailData = $email ?? $this->request->getPost('email');
    return redirect()->to(base_url('reset-feedback?email=' . urlencode($emailData)));
    }

    /**
     * Feedback setelah password reset email dikirim
     */
    public function resetFeedback()
    {
        $emailSent = $this->request->getGet('email');
        
        if (!$emailSent || !filter_var($emailSent, FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('login')
                ->with('error', 'Email tidak valid');
        }
        
        $adminEmail = config('Email')->fromEmail ?? 'admin@presensi.local';
        
        return $this->_render('auth/reset-feedback', [
            'email' => $emailSent,
            'admin_email' => $adminEmail,
        ]);
    }

    /**
     * Displays the Reset Password form.
     */
    public function resetPassword()
    {
        if ($this->config->activeResetter === null) {
            return redirect()->route('login')->with('error', lang('Auth.forgotDisabled'));
        }

        // Clear any previous reset session to prevent conflicts
        session()->remove(['reset_token', 'reset_email']);

        $token = $this->request->getGet('token');
        $email = $this->request->getGet('email');

        if (empty($token)) {
            return redirect()->to(url_to('login'))->with('error', 'Invalid or missing reset token. Please request a new password reset.');
        }

        // Validate token exists and isn't expired
        $users = model(UserModel::class);
        $user = $users->where('reset_hash', $token)->first();

        if (null === $user) {
            return redirect()->to(url_to('login'))->with('error', 'Invalid reset token. Please request a new password reset.');
        }

        // Verify email matches (if provided)
        if (!empty($email) && $email !== $user->email) {
            return redirect()->route('login')->with('error', 'Invalid email or token. Please request a new password reset.');
        }

        if (!empty($user->reset_expires) && time() > $user->reset_expires->getTimestamp()) {
            return redirect()->to(url_to('login'))->with('error', 'Reset token has expired. Please request a new password reset.');
        }

        // Store token in session for secure validation during reset
        session()->set('reset_token', $token);
        session()->set('reset_email', $user->email);

        return $this->_render($this->config->views['reset'], [
            'config' => $this->config,
            'token'  => $token,
            'email'  => $user->email,
        ]);
    }

    /**
     * Verifies the code with the email and saves the new password,
     * if they all pass validation.
     *
     * @return mixed
     */
    public function attemptReset()
    {
        if ($this->config->activeResetter === null) {
            return redirect()->route('login')->with('error', lang('Auth.forgotDisabled'));
        }

        // Get token and email from session (set during resetPassword display)
        $sessionToken = session()->get('reset_token');
        $sessionEmail = session()->get('reset_email');

        if (empty($sessionToken) || empty($sessionEmail)) {
            return redirect()->to(url_to('login'))->with('error', 'Your reset link has expired. Please request a new password reset by entering your email.');
        }

        $users = model(UserModel::class);

        // Verify token from session matches and user exists FIRST (before password validation)
        $user = $users->where('email', $sessionEmail)
            ->where('reset_hash', $sessionToken)
            ->first();

        if (null === $user) {
            // Clear session and redirect
            session()->remove(['reset_token', 'reset_email']);
            return redirect()->to(url_to('login'))->with('error', 'Reset link is invalid or has expired. Please request a new password reset.');
        }

        // Reset token still valid?
        if (! empty($user->reset_expires) && time() > $user->reset_expires->getTimestamp()) {
            session()->remove(['reset_token', 'reset_email']);
            return redirect()->to(url_to('login'))->with('error', 'Your reset link has expired. Please request a new one by entering your email.');
        }

        // Validate form input - only password fields
        // Use basic password rules without NothingPersonalValidator which needs email context
        $rules = [
            'password'     => 'required|min_length[8]|max_length[255]',
            'pass_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        // Log the reset attempt
        $users->logResetAttempt(
            $sessionEmail,
            $sessionToken,
            $this->request->getIPAddress(),
            (string) $this->request->getUserAgent()
        );

        try {
            // Success! Save the new password, and cleanup the reset hash.
            $user->password         = $this->request->getPost('password');
            $user->reset_hash       = null;
            $user->reset_at         = date('Y-m-d H:i:s');
            $user->reset_expires    = null;
            $user->force_pass_reset = false;
            $users->save($user);
        } catch (\Exception $e) {
            session()->remove(['reset_token', 'reset_email']);
            return redirect()->to(url_to('login'))->with('error', 'An error occurred while saving your new password. Please try again.');
        }

        // Clear session
        session()->remove(['reset_token', 'reset_email']);

        return redirect()->route('login')->with('message', 'Password reset successfully! Please log in with your new password.');
    }

    /**
     * Activate account.
     *
     * @return mixed
     */
    public function activateAccount()
    {
        $users = model(UserModel::class);

        // First things first - log the activation attempt.
        $users->logActivationAttempt(
            $this->request->getGet('token'),
            $this->request->getIPAddress(),
            (string) $this->request->getUserAgent()
        );

        $throttler = service('throttler');

        if ($throttler->check(md5($this->request->getIPAddress()), 2, MINUTE) === false) {
            return service('response')->setStatusCode(429)->setBody(lang('Auth.tooManyRequests', [$throttler->getTokentime()]));
        }

        $user = $users->where('activate_hash', $this->request->getGet('token'))
            ->where('active', 0)
            ->first();

        if (null === $user) {
            return redirect()->route('login')->with('error', lang('Auth.activationNoUser'));
        }

        $user->activate();

        $users->save($user);

        return redirect()->route('login')->with('message', lang('Auth.registerSuccess'));
    }

    /**
     * Resend activation account.
     *
     * @return mixed
     */
    public function resendActivateAccount($login = false)
    {
    if ($this->config->requireActivation === null) {
        return redirect()->route('login');
    }

    $throttler = service('throttler');

    if ($login == false) {
        if ($throttler->check(md5($this->request->getIPAddress()), 2, MINUTE) === false) {
            return service('response')->setStatusCode(429)->setBody(lang('Auth.tooManyRequests', [$throttler->getTokentime()]));
        }
        $login = urldecode($this->request->getGet('login'));
    }
    $type  = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    $users = model(UserModel::class);

    $user = $users->where($type, $login)
        ->where('active', 0)
        ->first();

    if (null === $user) {
        return redirect()->route('login')->with('error', lang('Auth.activationNoUser'));
    }

    $activator = service('activator');
    $sent      = $activator->send($user);

    if (!$sent) {
        return redirect()->back()->withInput()->with('error', $activator->error() ?? lang('Auth.unknownError'));
    }

    // Success!
    // return redirect()->route('login')->with('message', lang('Auth.activationSuccess'));
    return redirect()->to('/data-pegawai')->with('berhasil', 'Email aktivasi berhasil terkirim');
    }

    protected function _render(string $view, array $data = [])
    {
        return view($view, $data);
    }
}