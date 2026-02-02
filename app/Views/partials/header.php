<header class="navbar navbar-expand-md d-print-none custom-header">
  <div class="container-xl">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
      aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
      <a href="<?= base_url() ?>" class="brand-center d-flex align-items-center">
        <img src="<?= base_url('../assets/img/company/logo.png') ?>" width="150" height="44" alt="Logo"
          class="navbar-brand-image brand-logo me-2" style="height: 44px; width: auto;" />
        <span class="brand-text brand-text-custom" style="font-size: 1.35rem;"> Presen<span
            class="brand-highlight">Si</span>
        </span>
      </a>
    </h1>

    <div class="navbar-nav flex-row order-md-last">
      <div class="d-flex me-2">
        <a href="#" class="nav-link px-2" id="enable-dark-mode" title="Enable dark mode" data-bs-toggle="tooltip"
          data-bs-placement="bottom">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
          </svg>
        </a>
        <a href="#" class="nav-link px-2 d-none" id="enable-light-mode" title="Enable light mode"
          data-bs-toggle="tooltip" data-bs-placement="bottom">
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
          <div class="modal-title">Apakah Anda yakin ingin logout?</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
          <a href="<?= base_url('logout') ?>" class="btn btn-danger">Ya, logout</a>
        </div>
      </div>
    </div>
  </div>
</header>

<style>
.brand-text-custom {
  font-weight: 600;
  font-size: 1.15rem;
  letter-spacing: -0.02em;
  display: inline-block;
  color: var(--tblr-body-color);
  position: relative;
  text-decoration: none;
}

.brand-highlight {
  font-weight: 800;
  background: linear-gradient(135deg, var(--tblr-primary), var(--tblr-secondary));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  position: relative;
  display: inline-block;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.brand-highlight::before {
  content: '';
  position: absolute;
  top: -8px;
  right: -12px;
  width: 14px;
  height: 14px;

  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23f59f00'%3E%3Cpath d='M12 0L14.59 9.41L24 12L14.59 14.59L12 24L9.41 14.59L0 12L9.41 9.41L12 0Z'/%3E%3C/svg%3E");
  background-size: contain;
  background-repeat: no-repeat;
  opacity: 0;
  transform: scale(0) rotate(-45deg);
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);

  z-index: 1;
}

.brand-highlight::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 0%;
  height: 2px;
  background: var(--tblr-secondary);
  border-radius: 10px;
  transition: width 0.3s ease-out;
  opacity: 0.8;
}

.brand-center:hover .brand-highlight {
  transform: translateY(-2px) scale(1.05);

  filter: brightness(1.1);
}

.brand-center:hover .brand-highlight::before {
  opacity: 1;
  transform: scale(1) rotate(0deg);
}

.brand-center:hover .brand-highlight::after {
  width: 100%;
}

.brand-center:hover .brand-text-custom {
  color: var(--tblr-gray-700);
}

.navbar-brand-autodark .brand-text-custom {
  color: inherit;
}
</style>