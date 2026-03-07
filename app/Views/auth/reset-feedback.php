<?php
// \app\Views\auth\reset-feedback.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

// \app\Views\auth\reset-feedback.php
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <title>Cek Email Anda | PresenSi</title>
  <link href="<?= base_url('assets/css/tabler.min.css?1684106062') ?>" rel="stylesheet" />
  <link href="<?= base_url('assets/css/custom.css?1684106062') ?>" rel="stylesheet" />
  <link rel="icon" type="image/png" href="<?= base_url('assets/img/company/logo.png') ?>">
</head>

<body class="d-flex flex-column">
  <div class="page page-center">
    <div class="container container-tight py-4">
      <div class="text-center mb-4">
        <a href="<?= base_url() ?>" class="navbar-brand navbar-brand-autodark d-inline-flex align-items-center">
          <img src="<?= base_url('assets/img/company/logo.png') ?>" height="48" alt="Logo" class="me-2">
          <span style="font-size: 1.5rem; font-weight: 600;">Presen<span style="color: #dda518;">Si</span></span>
        </a>
      </div>

      <div class="card card-md">
        <div class="card-body text-center">
          <div class="mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-success" width="64" height="64"
              viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M9 12l2 2l4 -4" />
              <path d="M12 2c6.627 0 12 5.373 12 12a12 12 0 0 1 -12 12a12 12 0 0 1 -12 -12a12 12 0 0 1 12 -12" />
            </svg>
          </div>

          <h2 class="h3 mb-2">Email Reset Terkirim!</h2>

          <p class="text-muted mb-3">
            Email untuk reset password telah dikirim ke:
          </p>

          <div class="alert alert-info" role="alert">
            <strong><?= esc($email) ?></strong>
          </div>

          <div class="bg-light p-3 rounded mb-3 text-start">
            <h5 class="mb-2">Langkah Selanjutnya:</h5>
            <ol class="small mb-0">
              <li>Buka email Anda</li>
              <li>Cari email dari PresenSi</li>
              <li>Klik link reset password di dalam email</li>
              <li>Buat password baru Anda</li>
              <li>Login dengan password baru</li>
            </ol>
          </div>

          <p class="text-muted small mb-3">
            Link berlaku selama <strong>24 jam</strong>
          </p>

          <div class="alert alert-warning" role="alert">
            <strong>Tidak menerima email?</strong>
            <ul class="small mb-0 mt-2">
              <li>Periksa folder Spam/Junk Anda</li>
              <li>Pastikan email <code><?= esc($email) ?></code> sudah terdaftar</li>
            </ul>
          </div>

          <hr class="my-3">

          <p class="text-muted small">
            Jika email tidak diterima atau ada kendala, hubungi Admin:<br>
            <strong><?= esc($admin_email) ?></strong>
          </p>

          <a href="<?= base_url('/') ?>" class="btn btn-primary w-100 mt-3">
            Kembali ke Beranda
          </a>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= base_url('assets/js/tabler.min.js?1684106062') ?>" defer></script>
</body>

</html>