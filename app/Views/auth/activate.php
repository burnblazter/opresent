<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title><?= $title ?> | O-Present</title>
  <link href="<?= base_url('assets/css/tabler.min.css?1684106062') ?>" rel="stylesheet" />
  <link href="<?= base_url('assets/css/demo.min.css?1684106062') ?>" rel="stylesheet" />
  <link rel="website icon" type="png" href="<?= base_url('assets/img/company/logo.png') ?>">
  <link href="<?= base_url('assets/css/custom.css?1684106062') ?>" rel="stylesheet" />
  <style>
  body {
    font-feature-settings: "cv03", "cv04", "cv11";
  }
  </style>
</head>

<body class="d-flex flex-column">
  <script src="<?= base_url('assets/js/demo-theme.min.js?1684106062') ?>"></script>
  <div class="page page-center">
    <div class="container container-tight py-4">
      <div class="text-center mb-4">
        <a href="<?= base_url('/') ?>" class="navbar-brand navbar-brand-autodark">
          <img src="<?= base_url('assets/img/company/logo.png') ?>" height="60" alt="O-Present">
        </a>
      </div>
      <div class="card card-md">
        <div class="card-body">
          <h2 class="h2 text-center mb-4">Aktivasi Akun</h2>
          <p class="text-muted mb-4">
            Selamat datang! Silakan buat password untuk akun Anda.
          </p>

          <?php if (session()->has('errors')) : ?>
          <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
              <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24"
                  viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                  stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                  <path d="M12 8v4"></path>
                  <path d="M12 16h.01"></path>
                </svg>
              </div>
              <div>
                <?php foreach (session('errors') as $error) : ?>
                <div><?= esc($error) ?></div>
                <?php endforeach ?>
              </div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
          </div>
          <?php endif ?>

          <form action="<?= base_url('activate-account') ?>" method="post" autocomplete="off">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= esc($token) ?>">

            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="text" class="form-control" value="<?= esc($email) ?>" disabled>
            </div>

            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" class="form-control" value="<?= esc($username) ?>" disabled>
            </div>

            <div class="mb-3">
              <label class="form-label">Password Baru</label>
              <input type="password" name="password" class="form-control" placeholder="Masukkan password"
                autocomplete="new-password" required>
              <small class="form-hint">Minimal 8 karakter</small>
            </div>

            <div class="mb-3">
              <label class="form-label">Konfirmasi Password</label>
              <input type="password" name="pass_confirm" class="form-control" placeholder="Konfirmasi password"
                autocomplete="new-password" required>
            </div>

            <div class="form-footer">
              <button type="submit" class="btn btn-primary w-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                  stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M5 12l5 5l10 -10"></path>
                </svg>
                Aktivasi Akun
              </button>
            </div>
          </form>
        </div>
      </div>
      <div class="text-center text-muted mt-3">
        Sudah punya akun? <a href="<?= base_url('login') ?>" tabindex="-1">Login</a>
      </div>
    </div>
  </div>

  <script src="<?= base_url('assets/js/tabler.min.js?1684106062') ?>" defer></script>
</body>

</html>