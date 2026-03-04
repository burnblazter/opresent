<?php
// \app\Views\auth\change-email.php

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
  <title>Change Email | PresenSi</title>
  <link href="<?= base_url('assets/css/tabler.min.css?1684106062') ?>" rel="stylesheet" />
  <link href="<?= base_url('assets/css/custom.css?1684106062') ?>" rel="stylesheet" />
  <link rel="icon" type="image/png" href="<?= base_url('assets/img/company/logo.png') ?>">

  <script src="<?= base_url('assets/js/darkreader.min.js') ?>"></script>
  <script>
  const savedTheme = localStorage.getItem('theme-preference');
  if (savedTheme === 'dark') DarkReader.enable({
    brightness: 100,
    contrast: 100,
    sepia: 5
  });
  </script>

  <style>
  body {
    font-feature-settings: "cv03", "cv04", "cv11";
  }

  .quote-box {
    background: linear-gradient(135deg, rgba(30, 58, 138, 0.05), rgba(221, 165, 24, 0.05));
    border-left: 4px solid #1e3a8a;
    padding: 1rem 1.25rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
  }

  .quote-text {
    font-style: italic;
    color: #475569;
    margin-bottom: 0.5rem;
  }

  .quote-author {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 600;
  }

  .theme-switcher {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
  }

  .theme-switcher .nav-link {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    padding: 0.6rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }
  </style>
</head>

<body class="d-flex flex-column">
  <div class="theme-switcher d-flex gap-1">
    <a href="#" class="nav-link px-2" id="enable-dark-mode"><svg xmlns="http://www.w3.org/2000/svg" class="icon"
        width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
        <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
      </svg></a>
    <a href="#" class="nav-link px-2 d-none" id="enable-light-mode"><svg xmlns="http://www.w3.org/2000/svg" class="icon"
        width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
        <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
        <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
      </svg></a>
  </div>

  <div class="page page-center">
    <div class="container container-tight py-4">
      <div class="text-center mb-4">
        <a href="<?= base_url() ?>" class="navbar-brand navbar-brand-autodark d-inline-flex align-items-center">
          <img src="<?= base_url('assets/img/company/logo.png') ?>" height="48" alt="Logo" class="me-2">
          <span style="font-size: 1.5rem; font-weight: 600;">Presen<span
              style="color: #dda518; font-weight: 700;">Si</span></span>
        </a>
      </div>

      <div class="card card-md">
        <div class="card-body">
          <h2 class="h2 text-center mb-3">Change Your Email</h2>

          <?php if (isset($quote)): ?>
          <div class="quote-box">
            <div class="quote-text">"<?= esc($quote['text']) ?>"</div>
            <div class="quote-author">— <?= esc($quote['author']) ?></div>
          </div>
          <?php endif; ?>

          <?php if (session()->getFlashdata('berhasil')) : ?>
          <div class="alert alert-success alert-dismissible" role="alert">
            <div class="d-flex">
              <div><?= session()->getFlashdata('berhasil') ?></div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
          </div>
          <?php endif; ?>

          <?php if (session()->getFlashdata('gagal')) : ?>
          <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
              <div><?= session()->getFlashdata('gagal') ?></div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
          </div>
          <?php endif; ?>

          <form action="<?= url_to('update-email') ?>" method="post" autocomplete="off" novalidate>
            <?= csrf_field() ?>

            <div class="mb-3">
              <label class="form-label"><?= lang('Auth.token') ?></label>
              <input name="token" type="text"
                class="form-control <?php if (validation_show_error('token')) : ?>is-invalid<?php endif ?>"
                placeholder="<?= lang('Auth.token') ?>" value="<?= old('token', $token ?? '') ?>" autocomplete="off">
              <div class="invalid-feedback"><?= validation_show_error('token') ?></div>
            </div>

            <div class="mb-3">
              <label class="form-label"><?= lang('Auth.email') ?></label>
              <input name="email" type="email"
                class="form-control <?php if (validation_show_error('email')) : ?>is-invalid<?php endif ?>"
                placeholder="<?= lang('Auth.email') ?>" value="<?= old('email') ?>" autocomplete="off">
              <div class="invalid-feedback"><?= validation_show_error('email') ?></div>
            </div>

            <div class="mb-3">
              <label class="form-label">New Email Address</label>
              <input name="newEmail" type="text"
                class="form-control <?php if (validation_show_error('newEmail')) : ?>is-invalid<?php endif ?>"
                placeholder="New Email Address" autocomplete="off" value="<?= old('newEmail') ?>">
              <div class="invalid-feedback"><?= validation_show_error('newEmail') ?></div>
            </div>

            <div class="form-footer">
              <button type="submit" class="btn btn-primary w-100">Change Email</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= base_url('assets/js/tabler.min.js?1684106062') ?>" defer></script>
  <script>
  document.addEventListener("DOMContentLoaded", function() {
    const btnDark = document.getElementById('enable-dark-mode');
    const btnLight = document.getElementById('enable-light-mode');

    function updateUI(isDark) {
      isDark ? (btnDark?.classList.add('d-none'), btnLight?.classList.remove('d-none')) : (btnLight?.classList.add(
        'd-none'), btnDark?.classList.remove('d-none'));
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