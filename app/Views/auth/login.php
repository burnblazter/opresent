<?php
// \app\Views\auth\login.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Login | PresenSi</title>
  <link href="<?= base_url('assets/css/tabler.min.css?1684106062') ?>" rel="stylesheet" />
  <link href="<?= base_url('assets/css/custom.css?1684106062') ?>" rel="stylesheet" />
  <link rel="icon" type="image/png" href="<?= base_url('assets/img/company/logo.png') ?>">

  <script src="<?= base_url('assets/js/darkreader.min.js') ?>"></script>
  <script>
  const drOptions = {
    brightness: 100,
    contrast: 100,
    sepia: 5
  };
  const savedTheme = localStorage.getItem('theme-preference');
  if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    DarkReader.enable(drOptions);
  }
  </script>

  <style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-feature-settings: "cv03", "cv04", "cv11";
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
  }

  /* Hero Split Layout */
  .login-container {
    display: flex;
    min-height: 100vh;
    position: relative;
  }

  /* Left Panel - Branding & Storytelling */
  .brand-panel {
    flex: 1;
    background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
    padding: 3rem 4rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
  }

  .brand-panel::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(221, 165, 24, 0.15) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
  }

  .brand-panel::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -10%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
  }

  .brand-content {
    position: relative;
    z-index: 2;
  }

  .brand-logo-section {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 4rem;
  }

  .brand-logo-section img {
    width: 60px;
    height: 60px;
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.3));
  }

  .brand-name {
    font-size: 2rem;
    font-weight: 700;
    color: white;
    letter-spacing: -0.02em;
  }

  .brand-name .highlight {
    color: #dda518;
    position: relative;
  }

  .hero-content {
    max-width: 520px;
  }

  .hero-title {
    font-size: 2.75rem;
    font-weight: 800;
    color: white;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    letter-spacing: -0.03em;
  }

  .hero-subtitle {
    font-size: 1.125rem;
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.6;
    margin-bottom: 2.5rem;
    font-weight: 400;
  }

  .feature-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-bottom: 3rem;
  }

  .feature-item {
    display: flex;
    align-items: flex-start;
    gap: 0.875rem;
  }

  .feature-icon {
    width: 40px;
    height: 40px;
    background: rgba(221, 165, 24, 0.15);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .feature-icon svg {
    width: 22px;
    height: 22px;
    stroke: #dda518;
    stroke-width: 2.5;
  }

  .feature-text {
    flex: 1;
  }

  .feature-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: white;
    margin-bottom: 0.25rem;
  }

  .feature-desc {
    font-size: 0.8125rem;
    color: rgba(255, 255, 255, 0.6);
    line-height: 1.4;
  }

  .quote-section {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 1.75rem;
    position: relative;
    z-index: 2;
    min-height: 110px;
  }

  .quote-text {
    font-size: 1rem;
    font-style: italic;
    color: rgba(255, 255, 255, 0.95);
    margin-bottom: 0.75rem;
    line-height: 1.6;
  }

  .quote-author {
    font-size: 0.875rem;
    color: #dda518;
    font-weight: 600;
  }

  .quote-skeleton {
    background: linear-gradient(90deg, rgba(255, 255, 255, 0.1) 25%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.1) 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    border-radius: 6px;
    height: 1rem;
    margin-bottom: 0.5rem;
  }

  @keyframes loading {
    0% {
      background-position: 200% 0;
    }

    100% {
      background-position: -200% 0;
    }
  }

  /* Right Panel - Form */
  .form-panel {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: var(--custom-bg-cream);
    position: relative;
  }

  .form-container {
    width: 100%;
    max-width: 480px;
  }

  .form-header {
    text-align: center;
    margin-bottom: 2.5rem;
  }

  .form-title {
    font-size: 2rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 0.5rem;
    letter-spacing: -0.02em;
  }

  .form-subtitle {
    font-size: 0.9375rem;
    color: #64748b;
    font-weight: 400;
  }

  .login-card {
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.4);
    box-shadow: 0 20px 60px rgba(30, 58, 138, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.6);
    border-radius: 20px;
    padding: 2.5rem;
  }

  .form-group {
    margin-bottom: 1.5rem;
  }

  .form-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.5rem;
    display: block;
  }

  .form-control {
    width: 100%;
    padding: 0.875rem 1.125rem;
    font-size: 0.9375rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    background: #f8fafc;
    transition: all 0.2s ease;
    font-family: inherit;
  }

  .form-control:focus {
    outline: none;
    border-color: #1e3a8a;
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.08);
  }

  .form-control.is-invalid {
    border-color: #ef4444;
    background: #fef2f2;
  }

  .form-control.is-invalid:focus {
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.08);
  }

  .invalid-feedback {
    font-size: 0.8125rem;
    color: #ef4444;
    margin-top: 0.375rem;
    display: block;
  }

  .form-extras {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.75rem;
    font-size: 0.875rem;
  }

  .remember-me {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
  }

  .remember-me input {
    width: 18px;
    height: 18px;
    cursor: pointer;
  }

  .forgot-link {
    color: #1e3a8a;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s;
  }

  .forgot-link:hover {
    color: #dda518;
  }

  .btn-login {
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 14px rgba(30, 58, 138, 0.3);
    position: relative;
    overflow: hidden;
  }

  .btn-login::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
  }

  .btn-login:hover::before {
    left: 100%;
  }

  .btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(30, 58, 138, 0.4);
  }

  .btn-login:active {
    transform: translateY(0);
  }

  .divider {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 2rem 0;
    color: #94a3b8;
    font-size: 0.875rem;
  }

  .divider::before,
  .divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #e2e8f0;
  }

  .divider span {
    padding: 0 1rem;
  }

  .btn-playground {
    width: 100%;
    padding: 0.875rem;
    background: transparent;
    color: #64748b;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.9375rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    text-decoration: none;
  }

  .btn-playground:hover {
    border-color: #dda518;
    color: #dda518;
    background: rgba(221, 165, 24, 0.05);
  }

  .theme-switcher {
    position: fixed;
    top: 2rem;
    right: 2rem;
    z-index: 1000;
    display: flex;
    gap: 0.5rem;
  }

  .theme-btn {
    width: 44px;
    height: 44px;
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  .theme-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
  }

  .theme-btn svg {
    width: 20px;
    height: 20px;
    stroke: #334155;
  }

  /* Dark Mode Adjustments */
  [data-darkreader-scheme="dark"] .form-panel {
    background: #0f172a !important;
  }

  [data-darkreader-scheme="dark"] .login-card {
    background: rgba(30, 41, 59, 0.8) !important;
    border-color: rgba(71, 85, 105, 0.3) !important;
  }

  [data-darkreader-scheme="dark"] .form-title {
    color: #f1f5f9 !important;
  }

  [data-darkreader-scheme="dark"] .form-subtitle {
    color: #94a3b8 !important;
  }

  [data-darkreader-scheme="dark"] .form-control {
    background: rgba(15, 23, 42, 0.6) !important;
    border-color: rgba(71, 85, 105, 0.4) !important;
    color: #f1f5f9 !important;
  }

  [data-darkreader-scheme="dark"] .form-control:focus {
    background: rgba(15, 23, 42, 0.8) !important;
    border-color: #dda518 !important;
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .login-container {
      flex-direction: column;
    }

    .brand-panel {
      min-height: auto;
      padding: 2rem;
    }

    .brand-logo-section {
      margin-bottom: 1.5rem;
    }

    .brand-logo-section img {
      width: 48px;
      height: 48px;
    }

    .brand-name {
      font-size: 1.5rem;
    }

    .hero-content {
      display: none;
    }

    .feature-grid {
      display: none;
    }

    .quote-section {
      display: none;
    }

    .brand-panel::after {
      content: '"Si Pintar Urusan Presensi"';
      position: static;
      display: block;
      color: rgba(255, 255, 255, 0.8);
      font-size: 0.9rem;
      margin-top: 0.5rem;
      background: none;
      width: auto;
      height: auto;
    }

    .form-panel {
      flex: 1;
    }

    .theme-switcher {
      top: 1rem;
      right: 1rem;
    }
  }

  @media (max-width: 640px) {
    .login-card {
      padding: 2rem 1.5rem;
    }

    .form-title {
      font-size: 1.5rem;
    }

    .hero-title {
      font-size: 2rem;
    }

    .feature-grid {
      grid-template-columns: 1fr;
    }

    .brand-panel {
      padding: 2rem;
    }
  }
  </style>
</head>

<body>
  <!-- Theme Switcher -->
  <div class="theme-switcher">
    <a href="#" class="theme-btn" id="enable-dark-mode" title="Dark Mode">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
        stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
      </svg>
    </a>
    <a href="#" class="theme-btn d-none" id="enable-light-mode" title="Light Mode">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
        stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
        <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
      </svg>
    </a>
  </div>

  <div class="login-container">
    <!-- Left Panel - Branding -->
    <div class="brand-panel">
      <div class="brand-content">
        <div class="brand-logo-section">
          <img src="<?= base_url('assets/img/company/logo.png') ?>" alt="PresenSi Logo">
          <div class="brand-name">Presen<span class="highlight">Si</span></div>
        </div>

        <div class="hero-content">
          <h1 class="hero-title">"Si Pintar Urusan Presensi"</h1>
          <p class="hero-subtitle">Sistem presensi cerdas dengan Multi-Factor Authentication: Face Recognition, GPS
            Verification, dan Time-Based Validation untuk akurasi maksimal.</p>

          <div class="feature-grid">
            <div class="feature-item">
              <div class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                  <path d="M9 10l.01 0" />
                  <path d="M15 10l.01 0" />
                  <path d="M9.5 15a3.5 3.5 0 0 0 5 0" />
                </svg>
              </div>
              <div class="feature-text">
                <div class="feature-label">Face Recognition</div>
                <div class="feature-desc">AI verifikasi wajah otomatis dengan TensorFlow.js</div>
              </div>
            </div>

            <div class="feature-item">
              <div class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                  <path d="M12 10m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                  <path d="M12 2a10 10 0 0 0 10 10" />
                </svg>
              </div>
              <div class="feature-text">
                <div class="feature-label">GPS Verification</div>
                <div class="feature-desc">Validasi lokasi real-time untuk keamanan</div>
              </div>
            </div>

            <div class="feature-item">
              <div class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                  <path d="M12 7v5l3 3" />
                </svg>
              </div>
              <div class="feature-text">
                <div class="feature-label">Auto-Detection</div>
                <div class="feature-desc">Status kehadiran otomatis berbasis waktu</div>
              </div>
            </div>

            <div class="feature-item">
              <div class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                  <path d="M12 7v5l3 3" />
                </svg>
              </div>
              <div class="feature-text">
                <div class="feature-label">Real-Time Analytics</div>
                <div class="feature-desc">Dashboard monitoring dan insight otomatis</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quote Section -->
      <div class="quote-section" id="quote-container">
        <div class="quote-skeleton" style="width: 90%;"></div>
        <div class="quote-skeleton" style="width: 70%;"></div>
        <div class="quote-skeleton" style="width: 40%; height: 0.875rem;"></div>
      </div>
    </div>

    <!-- Right Panel - Form -->
    <div class="form-panel">
      <div class="form-container">
        <div class="form-header">
          <h2 class="form-title">Selamat Datang Kembali</h2>
          <p class="form-subtitle">Masuk untuk melanjutkan ke dashboard Anda</p>
        </div>

        <div class="login-card">
          <?= view('App\ThirdParty\MythAuth\Views\_message_block') ?>

          <form action="<?= url_to('login') ?>" method="post" autocomplete="off">
            <?= csrf_field() ?>

            <div class="form-group">
              <label class="form-label">
                <?php if ($config->validFields === ['email']) : ?>
                <?= lang('Auth.email') ?>
                <?php else : ?>
                <?= lang('Auth.emailOrUsername') ?>
                <?php endif; ?>
              </label>
              <input name="login" type="<?= $config->validFields === ['email'] ? 'email' : 'text' ?>"
                class="form-control <?php if (session('errors.login')) : ?>is-invalid<?php endif ?>"
                placeholder="<?= $config->validFields === ['email'] ? lang('Auth.email') : lang('Auth.emailOrUsername') ?>">
              <?php if (session('errors.login')) : ?>
              <span class="invalid-feedback"><?= session('errors.login') ?></span>
              <?php endif; ?>
            </div>

            <div class="form-group">
              <label class="form-label"><?= lang('Auth.password') ?></label>
              <input name="password" type="password"
                class="form-control <?php if (session('errors.password')) : ?>is-invalid<?php endif ?>"
                placeholder="<?= lang('Auth.password') ?>">
              <?php if (session('errors.password')) : ?>
              <span class="invalid-feedback"><?= session('errors.password') ?></span>
              <?php endif; ?>
            </div>

            <div class="form-extras">
              <?php if ($config->allowRemembering) : ?>
              <label class="remember-me">
                <input name="remember" type="checkbox" <?php if (old('remember')) : ?>checked<?php endif ?>>
                <span><?= lang('Auth.rememberMe') ?></span>
              </label>
              <?php endif; ?>

              <?php if ($config->activeResetter) : ?>
              <a href="<?= url_to('forgot') ?>" class="forgot-link"><?= lang('Auth.forgotYourPassword') ?></a>
              <?php endif; ?>
            </div>

            <button type="submit" class="btn-login"><?= lang('Auth.loginAction') ?></button>
          </form>

          <div class="divider">
            <span>atau</span>
          </div>

          <a href="<?= base_url('playground') ?>" class="btn-playground">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="icon icon-tabler icons-tabler-outline icon-tabler-monkeybar">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M3 21v-15l5 -3l5 3v15" />
              <path d="M8 21v-7" />
              <path d="M3 14h10" />
              <path d="M6 10a2 2 0 1 1 4 0" />
              <path d="M13 13c6 0 3 8 8 8" />
            </svg>
            Coba Tanpa Login (Playground)
          </a>
        </div>
      </div>

    </div>
  </div>

  <script src="<?= base_url('assets/js/tabler.min.js?1684106062') ?>" defer></script>
  <script>
  // Fetch quote
  fetch('<?= base_url('quote/random') ?>')
    .then(res => res.json())
    .then(data => {
      document.getElementById('quote-container').innerHTML = `
          <div class="quote-text">"${data.text}"</div>
          <div class="quote-author">— ${data.author}</div>
        `;
    })
    .catch(() => {
      document.getElementById('quote-container').innerHTML = `
          <div class="quote-text">"Keberhasilan adalah hasil dari persiapan, kerja keras, dan belajar dari kegagalan."</div>
          <div class="quote-author">— Colin Powell</div>
        `;
    });

  // Theme switcher
  document.addEventListener("DOMContentLoaded", function() {
    const btnDark = document.getElementById('enable-dark-mode');
    const btnLight = document.getElementById('enable-light-mode');

    function updateUI(isDark) {
      if (isDark) {
        btnDark?.classList.add('d-none');
        btnLight?.classList.remove('d-none');
      } else {
        btnLight?.classList.add('d-none');
        btnDark?.classList.remove('d-none');
      }
    }

    updateUI(DarkReader.isEnabled());

    btnDark?.addEventListener('click', (e) => {
      e.preventDefault();
      DarkReader.enable({
        brightness: 100,
        contrast: 100,
        sepia: 5
      });
      localStorage.setItem('theme-preference', 'dark');
      updateUI(true);
    });

    btnLight?.addEventListener('click', (e) => {
      e.preventDefault();
      DarkReader.disable();
      localStorage.setItem('theme-preference', 'light');
      updateUI(false);
    });
  });
  </script>
</body>

</html>