<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta19
* @link https://tabler.io
* Copyright 2018-2023 The Tabler Authors
* Copyright 2018-2023 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Reset Password | PresenSi</title>
  <!-- CSS files -->
  <link href="<?= base_url('../assets/css/tabler.min.css?1684106062') ?>" rel="stylesheet" />
  <link href="<?= base_url('../assets/css/tabler-flags.min.css?1684106062') ?>" rel="stylesheet" />
  <link href="<?= base_url('../assets/css/tabler-payments.min.css?1684106062') ?>" rel="stylesheet" />
  <link href="<?= base_url('../assets/css/tabler-vendors.min.css?1684106062') ?>" rel="stylesheet" />
  <link href="<?= base_url('../assets/css/demo.min.css?1684106062') ?>" rel="stylesheet" />
  <link href="<?= base_url('assets/css/custom.css?1684106062') ?>" rel="stylesheet" />
  <style>
  body {
    font-feature-settings: "cv03", "cv04", "cv11";
  }
  </style>

  <!-- Website Icon -->
  <link rel="website icon" type="png" href="<?= base_url('../assets/img/company/logo.png') ?>">
</head>

<body class="d-flex flex-column">
  <script src="<?= base_url('../assets/js/demo-theme.min.js?1684106062') ?>"></script>
  <div class="page page-center">
    <div class="container container-tight py-4">
      <div class="text-center mb-4">
        <a href="<?= base_url() ?>" class="navbar-brand navbar-brand-autodark align-items-center">
          <img src="<?= base_url('../assets/img/company/logo.png') ?>" height="36" alt="Smansa">
          <span>PresenSi</span>
        </a>
      </div>
      <div class="card card-md">
        <div class="card-body">
          <h2 class="h2 text-center mb-4"><?= lang('Auth.resetYourPassword') ?></h2>

          <?= view('Myth\Auth\Views\_message_block') ?>

          <form action="<?= url_to('reset-password') ?>" method="post" autocomplete="off" novalidate>
            <?= csrf_field() ?>

            <!-- Hidden token field -->
            <input type="hidden" name="token" value="<?= old('token', $token ?? '') ?>">

            <!-- Email field - disabled/readonly -->
            <div class="mb-3">
              <label class="form-label"><?= lang('Auth.email') ?></label>
              <input type="email" class="form-control" value="<?= isset($email) ? $email : '' ?>" disabled
                style="background-color: #e2e8f0; cursor: not-allowed; color: #64748b;">
              <small class="text-muted d-block mt-1">This email is associated with your account</small>
            </div>

            <div class="mb-3">
              <label class="form-label"><?= lang('Auth.newPassword') ?></label>
              <input name="password" type="password"
                class="form-control <?php if (session('errors.password')) : ?>is-invalid<?php endif ?>"
                placeholder="<?= lang('Auth.newPassword') ?>" autocomplete="off">
              <div class="invalid-feedback">
                <?= session('errors.password') ?>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label"><?= lang('Auth.newPasswordRepeat') ?></label>
              <input name="pass_confirm" type="password"
                class="form-control <?php if (session('errors.pass_confirm')) : ?>is-invalid<?php endif ?>"
                placeholder="<?= lang('Auth.newPasswordRepeat') ?>" autocomplete="off">
              <div class="invalid-feedback">
                <?= session('errors.pass_confirm') ?>
              </div>
            </div>

            <div class="form-footer">
              <button type="submit" class="btn btn-primary w-100"><?= lang('Auth.resetPassword') ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Libs JS -->
  <!-- Tabler Core -->
  <script src="<?= base_url('../assets/js/tabler.min.js?1684106062') ?>" defer></script>
  <script src="<?= base_url('../assets/js/demo.min.js?1684106062') ?>" defer></script>
</body>

</html>