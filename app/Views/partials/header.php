<header class="navbar navbar-expand-md d-print-none custom-header">
  <style>
  /* === BASE STYLES (Berlaku untuk Mobile & Desktop) === */
  .custom-header {
    position: relative;
    z-index: 2000;
    background: white;
    /* Pastikan ada background agar tidak transparan */
  }

  .custom-header .brand-center {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: inherit;
  }

  .custom-header .brand-text {
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.02em;
  }

  /* === DESKTOP ONLY: LOGO DEAD-CENTER === */
  /* Logika ini hanya jalan di layar > 768px */
  @media (min-width: 768px) {

    /* Jadikan container sebagai patokan koordinat */
    .custom-header .container-xl {
      position: relative;
    }

    /* Logo dicabut dari aliran layout dan ditaruh tepat di tengah container */
    .custom-header .brand-logo {
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      /* Geser titik tengah tepat ke center */
      z-index: 10;
    }

    /* Text Presensi tetap di posisi aslinya (kiri) */
    .custom-header .brand-text {
      margin-left: 0 !important;
      /* Reset margin jika perlu */
    }
  }

  /* === MOBILE ONLY === */
  /* Di HP, pastikan margin text rapi karena logo ada di sebelahnya */
  @media (max-width: 767.98px) {
    .custom-header .brand-text {
      margin-left: 0.5rem;
      /* Beri jarak sedikit dari logo */
    }
  }
  </style>

  <div class="container-xl">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
      aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
      <a href="<?= base_url() ?>" class="brand-center">
        <img src="<?= base_url('../assets/img/company/logo.png') ?>" width="110" height="32" alt="Smansa"
          class="navbar-brand-image brand-logo" />

        <span class="brand-text">PresenSi</span>
      </a>
    </h1>

    <div class="navbar-nav flex-row order-md-last">
      <div class="d-none d-md-flex">
        <a href="?theme=dark" class="nav-link px-4 hide-theme-dark" title="Enable dark mode" data-bs-toggle="tooltip"
          data-bs-placement="bottom">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
          </svg>
        </a>

        <a href="?theme=light" class="nav-link px-4 hide-theme-light" title="Enable light mode" data-bs-toggle="tooltip"
          data-bs-placement="bottom">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
            <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
          </svg>
        </a>
      </div>

      <div class="nav-item dropdown">
        <a href="<?= base_url('/profile') ?>" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
          aria-label="Open user menu">
          <span class="avatar avatar-sm"
            style="background-image: url(<?= base_url('./assets/img/user_profile/' . $user_profile->foto) ?>)"></span>
          <div class="d-none d-xl-block ps-2">
            <div><?= $user_profile->nama ?></div>
            <div class="mt-1 small text-muted"><?= $user_profile->jabatan ?></div>
          </div>
        </a>

        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
          <a href="<?= base_url('/profile') ?>" class="dropdown-item">Profile & Keamanan</a>
          <div class="dropdown-divider my-1"></div>
          <a href="<?= base_url('logout') ?>" class="dropdown-item" data-bs-toggle="modal"
            data-bs-target="#logout-modal">
            Logout
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="modal modal-blur fade" id="logout-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <div class="modal-title">Pekerjaan selesai! Apakah Anda yakin ingin logout?</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">
            Batal
          </button>
          <a href="<?= base_url('logout') ?>" class="btn btn-danger">Ya, logout</a>
        </div>
      </div>
    </div>
  </div>
</header>