<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kiosk Presensi</title>

  <link rel="stylesheet" href="<?= base_url('assets/css/tabler.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
  <!-- dark mode helper -->
  <script src="<?= base_url('assets/js/darkreader.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/quagga.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/human.js') ?>"></script>
  <script src="<?= base_url('assets/js/sweetalert.min.js') ?>"></script>

  <style>
  /* ============================================
     KIOSK MODE - ULTRA FAST & CLEAN
     ============================================ */

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, sans-serif;
    background-color: var(--custom-bg-cream);
    background-image:
      radial-gradient(circle, rgba(30, 58, 138, 0.15) 1.5px, transparent 1.5px),
      radial-gradient(circle, rgba(221, 165, 24, 0.08) 1px, transparent 1px);
    background-size: 40px 40px, 60px 60px;
    background-position: 0 0, 20px 20px;
    overflow: hidden;
    width: 100vw;
    height: 100vh;
  }

  .kiosk-container {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
  }

  /* ============================================
     HEADER - MINIMAL & FIXED
     ============================================ */
  .kiosk-header {
    background: rgba(255, 255, 255, 0.85);
    border-bottom: 1px solid rgba(30, 58, 138, 0.1);
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    backdrop-filter: blur(8px);
    box-shadow: 0 2px 8px rgba(30, 58, 138, 0.06);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 100;
    transition: transform 0.3s ease;
  }

  .kiosk-header.hidden {
    transform: translateY(-100%);
  }

  .header-left {
    display: flex;
    align-items: center;
    gap: 1.5rem;
  }

  .kiosk-brand {
    display: flex;
    align-items: center;
    /* vertical centering of logo + text */
    gap: 0.75rem;
    text-decoration: none;
  }

  .kiosk-logo {
    height: 36px;
    width: auto;
    display: block;
    /* remove inline image baseline gap */
  }

  .kiosk-title {
    margin: 0;
    /* reset default h1 margins */
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e3a8a;
    letter-spacing: -0.025em;
    /* ensure line-height same as logo so text doesn't float */
    line-height: 36px;
  }

  .kiosk-operator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(30, 58, 138, 0.05);
    border-radius: 8px;
    font-size: 0.875rem;
    color: #64748b;
  }

  .operator-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #1e3a8a;
  }

  .header-right {
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .kiosk-time {
    font-size: 1rem;
    font-weight: 600;
    color: #64748b;
  }

  .btn-header {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: 1px solid rgba(30, 58, 138, 0.2);
    background: white;
    color: #1e3a8a;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
  }

  .btn-header:hover {
    background: #1e3a8a;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2);
  }

  .btn-header svg {
    width: 18px;
    height: 18px;
  }

  .btn-logout {
    background: #dc3545;
    color: white;
    border-color: #dc3545;
  }

  .btn-logout:hover {
    background: #bb2d3b;
    border-color: #bb2d3b;
  }

  /* ============================================
     MAIN CONTENT - FULLSCREEN CANVAS
     ============================================ */
  .kiosk-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    position: relative;
    margin-top: 68px;
  }

  /* When the page is in fullscreen we still keep the 68px top margin
     so the header (logo/title) remains visible. The previous behaviour hid
     the header which made fullscreen feel monotonous and logo-less. */
  .kiosk-main.fullscreen {
    margin-top: 68px;
  }

  /* ============================================
     SCANNER CONTAINER - FULLSCREEN
     ============================================ */
  .scanner-container {
    width: 100%;
    height: 100%;
    position: relative;
  }

  #scanner-viewport {
    width: 100%;
    height: 100%;
    position: relative;
  }

  /* keep barcode camera centered and preserve aspect ratio instead of
     stretching to fill the container. Quagga tends to force 100% width/height
     so we override with auto dimensions and allow the element to scale within
     the parent box. */
  #scanner-viewport video,
  #scanner-viewport canvas {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: auto !important;
    height: auto !important;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    object-position: center;
    background: black;
  }

  /* quagga/drawing buffer appears on top of video; simply hide it */
  #scanner-viewport .drawingBuffer,
  /* also hide globally just in case other components inject it */
  .drawingBuffer {
    display: none !important;
  }

  .scanner-instruction {
    position: fixed;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(30, 58, 138, 0.95);
    color: white;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-size: 1.125rem;
    font-weight: 600;
    text-align: center;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(221, 165, 24, 0.5);
    z-index: 50;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
  }

  /* ============================================
     VERIFICATION MODE - FULLSCREEN
     ============================================ */
  .verification-container {
    display: none;
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 200;
    background-color: var(--custom-bg-cream);
    background-image:
      radial-gradient(circle, rgba(30, 58, 138, 0.15) 1.5px, transparent 1.5px),
      radial-gradient(circle, rgba(221, 165, 24, 0.08) 1px, transparent 1px);
    background-size: 40px 40px, 60px 60px;
    background-position: 0 0, 20px 20px;
  }

  .verification-header {
    background: rgba(255, 255, 255, 0.85);
    border-bottom: 1px solid rgba(30, 58, 138, 0.1);
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    backdrop-filter: blur(8px);
    box-shadow: 0 2px 8px rgba(30, 58, 138, 0.06);
  }

  .verification-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e3a8a;
  }

  .verification-content {
    height: calc(100% - 68px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    gap: 1.5rem;
  }

  .pegawai-info {
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow:
      0 8px 32px rgba(30, 58, 138, 0.08),
      inset 0 1px 0 rgba(255, 255, 255, 0.5);
    padding: 1.5rem 2rem;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 1.5rem;
  }

  .pegawai-foto {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #1e3a8a;
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2);
  }

  .pegawai-details {
    text-align: left;
  }

  .pegawai-nama {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e3a8a;
    margin-bottom: 0.25rem;
    letter-spacing: -0.025em;
  }

  .pegawai-nip {
    font-size: 1rem;
    color: #64748b;
    font-weight: 500;
  }

  .camera-container {
    position: relative;
    width: 100%;
    max-width: 640px;
    height: 480px;
    border-radius: 12px;
    overflow: hidden;
    transform: scaleX(-1);
    background: rgba(255, 255, 255, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow:
      0 8px 32px rgba(30, 58, 138, 0.08),
      inset 0 1px 0 rgba(255, 255, 255, 0.5);
    /* center the video feed inside the container */
    display: flex;
    justify-content: center;
    align-items: center;
  }

  #verification-video {
    width: auto;
    height: auto;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    object-position: center;
  }

  .face-status {
    padding: 1rem 2rem;
    border-radius: 12px;
    font-size: 1.125rem;
    font-weight: 600;
    border: 1px solid transparent;
    min-width: 300px;
    text-align: center;
  }

  .face-status.success {
    background: rgba(34, 197, 94, 0.1);
    color: #16a34a;
    border-color: rgba(34, 197, 94, 0.2);
  }

  .face-status.warning {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
    border-color: rgba(245, 158, 11, 0.2);
  }

  .face-status.danger {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    border-color: rgba(239, 68, 68, 0.2);
  }

  .btn-cancel {
    padding: 0.75rem 2rem;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .btn-cancel:hover {
    background: #bb2d3b;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
  }

  /* ============================================
     RESPONSIVE
     ============================================ */
  @media (max-width: 768px) {
    .kiosk-header {
      padding: 0.75rem 1rem;
    }

    .kiosk-title {
      font-size: 1rem;
    }

    .kiosk-operator {
      display: none;
    }

    .camera-container {
      height: 360px;
    }
  }

  /* ============================================
     LOGOUT MODAL
     ============================================ */
  .modal-content {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.3);
  }
  </style>
</head>

<body>
  <div class="kiosk-container">
    <!-- Header -->
    <div class="kiosk-header" id="kiosk-header">
      <div class="header-left">
        <a class="kiosk-brand">
          <img src="<?= base_url('assets/img/company/logo.png') ?>" alt="Logo" class="kiosk-logo">
          <h1 class="kiosk-title">Kiosk | PresenSi</h1>
        </a>

        <div class="kiosk-operator">
          <img src="<?= base_url('assets/img/user_profile/' . esc($operator->foto)) ?>" alt="Operator"
            class="operator-avatar">
          <span><?= esc($operator->nama) ?></span>
        </div>
      </div>

      <div class="header-right">
        <!-- dark mode toggles (uses DarkReader) -->
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
        <div class="kiosk-time" id="current-time"></div>

        <button class="btn-header" id="btn-fullscreen" onclick="toggleFullscreen()">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3" />
          </svg>
          <span id="fullscreen-text">Fullscreen</span>
        </button>

        <button class="btn-header btn-logout" id="btn-logout" data-bs-toggle="modal" data-bs-target="#logout-modal">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" y1="12" x2="9" y2="12" />
          </svg>
          Logout
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="kiosk-main" id="kiosk-main">
      <!-- Scanner Mode -->
      <div class="scanner-container" id="scanner-container">
        <div id="scanner-viewport"></div>
        <div class="scanner-instruction">
          📱 Arahkan Barcode ke Kamera
        </div>
      </div>
    </div>

    <!-- Verification Mode -->
    <div class="verification-container" id="verification-container">
      <div class="verification-header">
        <div class="verification-title">🔐 Verifikasi Wajah</div>
        <div class="kiosk-time" id="current-time-verify"></div>
      </div>

      <div class="verification-content">
        <div class="pegawai-info">
          <img src="" alt="Foto" class="pegawai-foto" id="pegawai-foto">
          <div class="pegawai-details">
            <div class="pegawai-nama" id="pegawai-nama"></div>
            <div class="pegawai-nip" id="pegawai-nip"></div>
          </div>
        </div>

        <div class="camera-container">
          <video id="verification-video" autoplay playsinline></video>
          <canvas id="capture-canvas" style="display:none;"></canvas>
        </div>

        <div class="face-status" id="face-status"></div>

        <button class="btn-cancel" onclick="resetToScanner()">❌ Batal</button>
      </div>
    </div>
  </div>

  <!-- Logout Modal -->
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

  <script src="<?= base_url('assets/js/tabler.min.js') ?>"></script>

  <script>
  // dark mode initialization (copied from production template)
  const drOptions = {
    brightness: 100,
    contrast: 100,
    sepia: 5
  };
  const savedTheme = localStorage.getItem('theme-preference');
  const sysDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

  if (savedTheme === 'dark' || (!savedTheme && sysDark)) {
    DarkReader.enable(drOptions);
  } else {
    DarkReader.disable();
  }

  document.addEventListener('DOMContentLoaded', function() {
    const btnDark = document.getElementById('enable-dark-mode');
    const btnLight = document.getElementById('enable-light-mode');

    function updateUI(isDark) {
      if (isDark) {
        if (btnDark) btnDark.classList.add('d-none');
        if (btnLight) btnLight.classList.remove('d-none');
      } else {
        if (btnLight) btnLight.classList.add('d-none');
        if (btnDark) btnDark.classList.remove('d-none');
      }
    }

    updateUI(DarkReader.isEnabled());

    if (btnDark) {
      btnDark.addEventListener('click', (e) => {
        e.preventDefault();
        DarkReader.enable(drOptions);
        localStorage.setItem('theme-preference', 'dark');
        updateUI(true);
      });
    }
    if (btnLight) {
      btnLight.addEventListener('click', (e) => {
        e.preventDefault();
        DarkReader.disable();
        localStorage.setItem('theme-preference', 'light');
        updateUI(false);
      });
    }
  });

  // ==================== STATE ====================
  const state = {
    currentMode: 'scanner',
    currentPegawai: null,
    human: null,
    isModelLoaded: false,
    isFaceVerifying: false,
    isProcessingScan: false,
    lastScanTime: 0,
    detectionInterval: null,
    streamVerification: null,
    isFullscreen: false,
    baseUrl: '<?= base_url() ?>',
    csrfToken: '<?= csrf_token() ?>',
    csrfHash: '<?= csrf_hash() ?>'
  };

  // ==================== PRELOAD HUMAN.JS ====================
  async function preloadHumanJS() {
    try {
      console.log('🚀 Preloading Human.js...');
      state.human = new Human.Human({
        modelBasePath: state.baseUrl + 'assets/models/',
        backend: 'wasm',
        face: {
          enabled: true,
          detector: {
            maxDetected: 1,
            minConfidence: 0.62,
            rotation: true
          },
          description: {
            enabled: true
          },
          mesh: {
            enabled: true
          }
        }
      });
      await state.human.load();
      await state.human.warmup();
      state.isModelLoaded = true;
      console.log('✅ Human.js ready!');
    } catch (error) {
      console.error('❌ Preload Human.js failed:', error);
    }
  }

  // ==================== CLOCK ====================
  function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    });
    const clockEl = document.getElementById('current-time');
    const clockVerifyEl = document.getElementById('current-time-verify');
    if (clockEl) clockEl.textContent = timeString;
    if (clockVerifyEl) clockVerifyEl.textContent = timeString;
  }
  setInterval(updateClock, 1000);
  updateClock();

  // ==================== FULLSCREEN MANAGEMENT ====================
  function toggleFullscreen() {
    const elem = document.documentElement;
    const btnText = document.getElementById('fullscreen-text');
    const header = document.getElementById('kiosk-header');
    const btnLogout = document.getElementById('btn-logout');
    const kioskMain = document.getElementById('kiosk-main');

    if (!document.fullscreenElement) {
      elem.requestFullscreen().then(() => {
        state.isFullscreen = true;
        btnText.textContent = 'Exit Fullscreen';
        // leave the header visible so the logo stays on screen; only hide
        // the logout button to avoid accidental clicks
        //header.classList.add('hidden');
        btnLogout.style.display = 'none';
        kioskMain.classList.add('fullscreen');
      }).catch(err => {
        console.error('Fullscreen error:', err);
      });
    } else {
      document.exitFullscreen().then(() => {
        state.isFullscreen = false;
        btnText.textContent = 'Fullscreen';
        //header.classList.remove('hidden');
        btnLogout.style.display = 'flex';
        kioskMain.classList.remove('fullscreen');
      });
    }
  }

  document.addEventListener('fullscreenchange', () => {
    const btnText = document.getElementById('fullscreen-text');
    const btnLogout = document.getElementById('btn-logout');
    const kioskMain = document.getElementById('kiosk-main');

    if (!document.fullscreenElement) {
      state.isFullscreen = false;
      btnText.textContent = 'Fullscreen';
      btnLogout.style.display = 'flex';
      kioskMain.classList.remove('fullscreen');
    }
  });

  // ==================== OPTIMIZED SCANNER ====================
  function initScanner() {
    const viewport = document.getElementById('scanner-viewport');
    if (viewport) viewport.innerHTML = '';

    state.isProcessingScan = false;
    state.lastScanTime = 0;

    Quagga.init({
      inputStream: {
        name: "Live",
        type: "LiveStream",
        target: viewport,
        constraints: {
          width: {
            ideal: 1920
          },
          height: {
            ideal: 1080
          },
          facingMode: "environment"
        }
      },
      locator: {
        patchSize: "medium",
        halfSample: true
      },
      numOfWorkers: navigator.hardwareConcurrency || 4,
      frequency: 5,
      decoder: {
        readers: ["code_128_reader"],
        multiple: false
      },
      locate: true
    }, function(err) {
      if (err) {
        console.error('Quagga error:', err);
        Swal.fire({
          icon: 'error',
          title: 'Scanner Error',
          text: err.message,
          timer: 3000,
          timerProgressBar: true,
          showConfirmButton: false,
          confirmButtonColor: '#1e3a8a'
        });
        return;
      }
      console.log('✅ Scanner ready');
      Quagga.start();
    });

    Quagga.onDetected(handleBarcodeDetected);
  }

  function handleBarcodeDetected(result) {
    const now = Date.now();

    // Anti-spam: minimal 2 detik antar scan
    if (now - state.lastScanTime < 2000) return;

    if (state.isProcessingScan) return;
    if (!result || !result.codeResult || !result.codeResult.code) return;

    const code = result.codeResult.code;
    if (code.length < 3) return;

    state.isProcessingScan = true;
    state.lastScanTime = now;

    try {
      Quagga.stop();
      Quagga.offDetected(handleBarcodeDetected);
    } catch (e) {}

    cariPegawai(code);
  }

  // ==================== CARI PEGAWAI ====================
  async function cariPegawai(nomor_induk) {
    try {
      const formData = new FormData();
      formData.append('nomor_induk', nomor_induk);
      formData.append(state.csrfToken, state.csrfHash);

      const response = await fetch(state.baseUrl + 'kiosk/cari-pegawai', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      const data = await response.json();
      if (data.csrf_hash) state.csrfHash = data.csrf_hash;

      if (!data.success) {
        Swal.fire({
          icon: 'error',
          title: 'Tidak Ditemukan',
          text: data.message,
          timer: 2000,
          showConfirmButton: false
        });
        resetToScanner();
        return;
      }

      state.currentPegawai = data.data;

      if (!state.currentPegawai.descriptors || state.currentPegawai.descriptors.length === 0) {
        Swal.fire({
          icon: 'warning',
          title: 'Wajah Belum Terdaftar',
          text: 'Wajah pegawai belum terdaftar. Hubungi admin.',
          timer: 3000,
          timerProgressBar: true,
          showConfirmButton: false,
          confirmButtonColor: '#1e3a8a'
        });
        resetToScanner();
        return;
      }

      showVerificationMode();

    } catch (error) {
      console.error(error);
      Swal.fire({
        icon: 'error',
        title: 'Koneksi Error',
        text: 'Terjadi kesalahan jaringan. Silakan coba lagi.',
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        confirmButtonColor: '#1e3a8a'
      });
      resetToScanner();
    }
  }

  // ==================== VERIFICATION MODE ====================
  async function showVerificationMode() {
    document.getElementById('scanner-container').style.display = 'none';
    document.getElementById('verification-container').style.display = 'block';

    document.getElementById('pegawai-foto').src = state.currentPegawai.foto;
    document.getElementById('pegawai-nama').textContent = state.currentPegawai.nama;
    document.getElementById('pegawai-nip').textContent = 'Nomor Induk: ' + state.currentPegawai.nomor_induk;

    updateFaceStatus('Memuat kamera...', 'warning');

    await initVerificationCamera();

    // Human.js sudah di-preload, langsung pakai!
    if (!state.isModelLoaded) {
      await preloadHumanJS();
    }

    startFaceDetection();
  }

  async function initVerificationCamera() {
    try {
      state.streamVerification = await navigator.mediaDevices.getUserMedia({
        video: {
          facingMode: 'user',
          width: {
            ideal: 640
          },
          height: {
            ideal: 480
          }
        }
      });
      const video = document.getElementById('verification-video');
      video.srcObject = state.streamVerification;
      await video.play();
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Kamera Error',
        text: 'Gagal mengakses kamera: ' + error.message,
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        confirmButtonColor: '#1e3a8a'
      });
      resetToScanner();
    }
  }

  // ==================== FACE DETECTION ====================
  async function startFaceDetection() {
    const video = document.getElementById('verification-video');

    state.detectionInterval = setInterval(async () => {
      if (state.isFaceVerifying) return;

      try {
        const result = await state.human.detect(video);

        if (result.face && result.face.length > 0) {
          const face = result.face[0];
          if (!face.embedding) return;

          let bestMatch = {
            score: 0
          };
          for (const saved of state.currentPegawai.descriptors) {
            const score = state.human.match.similarity(face.embedding, saved.descriptor);
            if (score > bestMatch.score) bestMatch.score = score;
          }

          if (bestMatch.score >= 0.62) {
            updateFaceStatus(`✅ Wajah cocok! (${(bestMatch.score * 100).toFixed(1)}%)`, 'success');

            if (!state.isFaceVerifying) {
              state.isFaceVerifying = true;
              clearInterval(state.detectionInterval);
              state.detectionInterval = null;

              setTimeout(() => {
                captureAndSubmit(bestMatch.score);
              }, 300);
            }
          } else {
            updateFaceStatus(`⚠️ Wajah tidak cocok (${(bestMatch.score * 100).toFixed(1)}%)`, 'warning');
          }
        } else {
          updateFaceStatus('👤 Tidak ada wajah terdeteksi', 'danger');
        }
      } catch (error) {
        console.error('Detection error:', error);
      }
    }, 200);
  }

  function updateFaceStatus(message, type) {
    const statusEl = document.getElementById('face-status');
    if (statusEl) {
      statusEl.textContent = message;
      statusEl.className = 'face-status ' + type;
    }
  }

  // ==================== CAPTURE & SUBMIT ====================
  async function captureAndSubmit(similarity) {
    if (!state.currentPegawai || !state.currentPegawai.id_pegawai) {
      console.warn("Data pegawai sudah kosong.");
      return;
    }

    updateFaceStatus('📸 Menyimpan...', 'success');

    const video = document.getElementById('verification-video');
    const canvas = document.getElementById('capture-canvas');
    const ctx = canvas.getContext('2d');

    canvas.width = video.videoWidth || 640;
    canvas.height = video.videoHeight || 480;

    ctx.translate(canvas.width, 0);
    ctx.scale(-1, 1);
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    const imageData = canvas.toDataURL('image/jpeg', 0.8);

    const now = new Date();
    const currentTime = now.getHours() * 60 + now.getMinutes();
    const jamPulang = '<?= $lokasi->jam_pulang ?>';
    const [hh, mm] = jamPulang.split(':');
    const jamPulangMenit = parseInt(hh) * 60 + parseInt(mm);

    const mode = (currentTime < jamPulangMenit) ? 'masuk' : 'keluar';

    try {
      const formData = new FormData();
      formData.append('id_pegawai', state.currentPegawai.id_pegawai);
      formData.append('mode', mode);
      formData.append('image', imageData);
      formData.append('face_verified', 'true');
      formData.append('face_similarity', similarity);
      formData.append(state.csrfToken, state.csrfHash);

      const response = await fetch(state.baseUrl + 'kiosk/simpan-presensi', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      const result = await response.json();
      if (result.csrf_hash) state.csrfHash = result.csrf_hash;

      if (result.success || result.duplicate) {
        showConfirmation(result.data, mode, result.duplicate);
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: result.message,
          timer: 3000,
          timerProgressBar: true,
          showConfirmButton: false,
          confirmButtonColor: '#1e3a8a'
        });
        resetToScanner();
      }

    } catch (error) {
      console.error(error);
      Swal.fire({
        icon: 'error',
        title: 'Koneksi Error',
        text: 'Terjadi kesalahan koneksi.',
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        confirmButtonColor: '#1e3a8a'
      });
      resetToScanner();
    }
  }

  // ==================== CONFIRMATION ====================
  function showConfirmation(data, mode, isDuplicate) {
    if (state.streamVerification) {
      state.streamVerification.getTracks().forEach(t => t.stop());
    }
    document.getElementById('verification-container').style.display = 'none';

    const isLate = data.status.includes('Terlambat');
    const icon = isDuplicate ? 'info' : (isLate ? 'warning' : 'success');
    const title = isDuplicate ? 'Sudah Tercatat' : `Presensi ${mode === 'masuk' ? 'Masuk' : 'Keluar'} Berhasil!`;

    Swal.fire({
      icon: icon,
      title: title,
      html: `
        <div style="font-size: 1.125rem; margin: 1rem 0;">
          <strong>${data.nama}</strong><br>
          <span style="color: #64748b;">Nomor Induk: ${data.nomor_induk}</span><br>
          <span style="color: #64748b;">Jam: ${data.jam}</span>
        </div>
        <div style="
          padding: 0.75rem 1.5rem;
          background: ${isLate ? 'rgba(245, 158, 11, 0.1)' : 'rgba(34, 197, 94, 0.1)'};
          border: 1px solid ${isLate ? 'rgba(245, 158, 11, 0.3)' : 'rgba(34, 197, 94, 0.3)'};
          color: ${isLate ? '#d97706' : '#16a34a'};
          border-radius: 12px;
          font-weight: 600;
        ">
          ${data.status}
        </div>
      `,
      timer: 3000,
      showConfirmButton: false,
      allowOutsideClick: false,
      didOpen: () => {
        // Auto-reset setelah 3 detik
        setTimeout(() => {
          resetToScanner();
        }, 3000);
      }
    });
  }

  // ==================== RESET - ULTRA FAST ====================
  function resetToScanner() {
    if (state.streamVerification) {
      state.streamVerification.getTracks().forEach(t => t.stop());
      state.streamVerification = null;
    }

    if (state.detectionInterval) {
      clearInterval(state.detectionInterval);
      state.detectionInterval = null;
    }

    if (typeof Quagga !== 'undefined') {
      try {
        Quagga.stop();
      } catch (e) {}
    }

    const viewport = document.getElementById('scanner-viewport');
    if (viewport) viewport.innerHTML = '';

    state.currentPegawai = null;
    state.isFaceVerifying = false;
    state.isProcessingScan = false;

    document.getElementById('verification-container').style.display = 'none';
    document.getElementById('scanner-container').style.display = 'block';

    // INSTANT RESTART - NO DELAY!
    setTimeout(() => {
      initScanner();
    }, 100);
  }

  // ==================== INIT ====================
  document.addEventListener('DOMContentLoaded', function() {
    // Preload Human.js di background
    preloadHumanJS();

    // Start scanner
    initScanner();
  });

  window.addEventListener('beforeunload', function() {
    if (state.streamVerification) {
      state.streamVerification.getTracks().forEach(t => t.stop());
    }
    if (typeof Quagga !== 'undefined') {
      try {
        Quagga.stop();
      } catch (e) {}
    }
  });
  </script>
</body>

</html>