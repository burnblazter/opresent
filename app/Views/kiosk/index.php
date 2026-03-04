<?php
// \app\Views\kiosk\index.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=0.5">
  <title>Kiosk | PresenSi</title>
  <link rel="preload" as="image" href="<?= base_url('assets/img/company/logo.png') ?>">
  <link rel="icon" type="image/png" href="<?= base_url('assets/img/company/logo.png') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/tabler.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
  <script src="<?= base_url('assets/js/darkreader.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/quagga.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/human.js') ?>"></script>
  <script src="<?= base_url('assets/js/sweetalert.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/qrcode.min.js') ?>"></script>

  <style>
  /* ============================================
     KIOSK MODE - SEAMLESS SINGLE PAGE
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
    zoom: 0.9;
  }

  .kiosk-container {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
  }

  /* ============================================
     HEADER
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
  }

  .header-left {
    display: flex;
    align-items: center;
    gap: 1.5rem;
  }

  .kiosk-brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
  }

  .kiosk-logo {
    height: 36px;
    width: auto;
    display: block;
  }

  .kiosk-title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e3a8a;
    letter-spacing: -0.025em;
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
     MAIN LAYOUT - SIDEBAR + CONTENT
     ============================================ */
  .kiosk-main {
    flex: 1;
    display: flex;
    margin-top: 68px;
    height: calc(100vh - 68px);
    overflow: hidden;
  }

  /* SIDEBAR - PRESENSI TERAKHIR */
  .sidebar-recent {
    width: 320px;
    background: rgba(255, 255, 255, 0.9);
    border-right: 1px solid rgba(30, 58, 138, 0.1);
    backdrop-filter: blur(8px);
    display: flex;
    flex-direction: column;
    transition: transform 0.3s ease;
  }

  .sidebar-header {
    padding: 1.5rem;
    border-bottom: 1px solid rgba(30, 58, 138, 0.1);
  }

  .sidebar-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1e3a8a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .sidebar-subtitle {
    font-size: 0.75rem;
    color: #64748b;
    margin-top: 0.25rem;
  }

  .sidebar-content {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
  }

  .recent-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: white;
    border: 1px solid rgba(30, 58, 138, 0.1);
    border-radius: 8px;
    margin-bottom: 0.75rem;
    animation: slideIn 0.3s ease;
  }

  @keyframes slideIn {
    from {
      opacity: 0;
      transform: translateX(-10px);
    }

    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  .recent-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #1e3a8a;
  }

  .recent-info {
    flex: 1;
  }

  .recent-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e3a8a;
    margin: 0;
    line-height: 1.2;
  }

  .recent-time {
    font-size: 0.75rem;
    color: #64748b;
    margin-top: 0.25rem;
  }

  .recent-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
  }

  .recent-badge.masuk {
    background: rgba(34, 197, 94, 0.1);
    color: #16a34a;
  }

  .recent-badge.keluar {
    background: rgba(59, 130, 246, 0.1);
    color: #2563eb;
  }

  .sidebar-empty {
    text-align: center;
    padding: 2rem 1rem;
    color: #94a3b8;
    font-size: 0.875rem;
  }

  /* MAIN CONTENT AREA */
  .content-area {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
  }

  /* ============================================
     STATE TRANSITIONS - SEAMLESS
     ============================================ */
  .state-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
  }

  .state-container.active {
    opacity: 1;
    pointer-events: auto;
  }

  /* ============================================
     SCANNER STATE
     ============================================ */
  .scanner-view {
    width: 100%;
    height: 100%;
    position: relative;
  }

  #scanner-viewport {
    width: 100%;
    height: 100%;
    position: relative;
  }

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
    background: black;
  }

  .drawingBuffer {
    display: none !important;
  }

  .scanner-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 300px;
    height: 300px;
    border: 3px solid #dda518;
    border-radius: 16px;
    pointer-events: none;
  }

  .scanner-overlay::before,
  .scanner-overlay::after,
  .scanner-overlay>span::before,
  .scanner-overlay>span::after {
    content: '';
    position: absolute;
    width: 30px;
    height: 30px;
    border: 4px solid #dda518;
  }

  .scanner-overlay::before {
    top: -3px;
    left: -3px;
    border-right: none;
    border-bottom: none;
  }

  .scanner-overlay::after {
    top: -3px;
    right: -3px;
    border-left: none;
    border-bottom: none;
  }

  .scanner-overlay>span::before {
    bottom: -3px;
    left: -3px;
    border-right: none;
    border-top: none;
  }

  .scanner-overlay>span::after {
    bottom: -3px;
    right: -3px;
    border-left: none;
    border-top: none;
  }

  .scanner-instruction {
    position: absolute;
    bottom: 3rem;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(30, 58, 138, 0.95);
    color: white;
    padding: 1.25rem 2.5rem;
    border-radius: 12px;
    font-size: 1.125rem;
    font-weight: 600;
    text-align: center;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(221, 165, 24, 0.5);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    max-width: 90%;
  }

  /* ============================================
     VERIFICATION STATE
     ============================================ */
  .verification-view {
    width: 100%;
    max-width: 1000px;
    padding: 2rem;
  }

  .pegawai-card {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(30, 58, 138, 0.1);
    box-shadow: 0 4px 16px rgba(30, 58, 138, 0.08);
    padding: 1.5rem;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
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
    flex: 1;
  }

  .pegawai-nama {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e3a8a;
    margin: 0 0 0.25rem 0;
  }

  .pegawai-nip {
    font-size: 1rem;
    color: #64748b;
    font-weight: 500;
  }

  /* INSTRUCTION PANEL */
  .instruction-panel {
    background: #1e3a8a;
    color: white;
    padding: 1.25rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    border: 2px solid #dda518;
  }

  .instruction-title {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .instruction-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .instruction-list li {
    padding: 0.5rem 0;
    padding-left: 1.5rem;
    position: relative;
    font-size: 0.875rem;
    opacity: 0.95;
  }

  .instruction-list li::before {
    content: '✓';
    position: absolute;
    left: 0;
    color: #dda518;
    font-weight: bold;
  }

  .camera-wrapper {
    position: relative;
    width: 100%;
    max-width: 640px;
    height: 480px;
    margin: 0 auto;
    border-radius: 12px;
    overflow: hidden;
    transform: scaleX(-1);
    background: rgba(255, 255, 255, 0.7);
    border: 1px solid rgba(30, 58, 138, 0.1);
    box-shadow: 0 4px 16px rgba(30, 58, 138, 0.08);
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
  }

  .face-status-panel {
    text-align: center;
    padding: 1rem;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    margin-top: 1.5rem;
    border: 2px solid transparent;
    min-height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .face-status-panel.info {
    background: rgba(59, 130, 246, 0.1);
    color: #2563eb;
    border-color: rgba(59, 130, 246, 0.2);
  }

  .face-status-panel.success {
    background: rgba(34, 197, 94, 0.1);
    color: #16a34a;
    border-color: rgba(34, 197, 94, 0.2);
  }

  .face-status-panel.warning {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
    border-color: rgba(245, 158, 11, 0.2);
  }

  .face-status-panel.danger {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    border-color: rgba(239, 68, 68, 0.2);
  }

  .action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 1.5rem;
  }

  .btn-action {
    padding: 0.75rem 2rem;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .btn-cancel {
    background: #dc3545;
    color: white;
  }

  .btn-cancel:hover {
    background: #bb2d3b;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
  }

  /* ============================================
     FAILURE HELP STATE
     ============================================ */
  .failure-help {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(239, 68, 68, 0.2);
    padding: 2rem;
    border-radius: 16px;
    max-width: 600px;
    margin: 0 auto;
    box-shadow: 0 4px 16px rgba(30, 58, 138, 0.08);
  }

  .failure-icon {
    font-size: 4rem;
    text-align: center;
    margin-bottom: 1rem;
  }

  .failure-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #dc2626;
    text-align: center;
    margin-bottom: 1rem;
  }

  .failure-message {
    font-size: 1rem;
    color: #64748b;
    text-align: center;
    margin-bottom: 1.5rem;
    line-height: 1.6;
  }

  .qr-section {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    border: 2px dashed #dda518;
    text-align: center;
    margin-bottom: 1.5rem;
  }

  .qr-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e3a8a;
    margin-bottom: 1rem;
  }

  #qrcode-container {
    display: inline-block;
    padding: 1rem;
    background: white;
    border-radius: 8px;
  }

  .qr-url {
    font-size: 0.75rem;
    color: #64748b;
    margin-top: 0.75rem;
    word-break: break-all;
  }

  /* ============================================
     RESPONSIVE
     ============================================ */
  @media (max-width: 1024px) {
    .sidebar-recent {
      width: 280px;
    }
  }

  @media (max-width: 768px) {
    .sidebar-recent {
      position: fixed;
      left: -320px;
      top: 68px;
      height: calc(100vh - 68px);
      z-index: 90;
    }

    .sidebar-recent.active {
      left: 0;
    }

    .kiosk-operator {
      display: none;
    }

    .camera-wrapper {
      height: 360px;
    }
  }

  /* ============================================
     MODAL STYLES
     ============================================ */
  .modal-content {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.3);
  }

  /* ============================================
     HIDE LOGOUT BUTTON IN FULLSCREEN
     ============================================ */
  :fullscreen .btn-logout {
    display: none !important;
  }

  :-webkit-full-screen .btn-logout {
    display: none !important;
  }
  </style>
</head>

<body>
  <div class="kiosk-container">
    <!-- Header -->
    <div class="kiosk-header">
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
        <a href="#" class="nav-link px-2" id="enable-dark-mode" title="Enable dark mode">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
            stroke-width="2" stroke="currentColor" fill="none">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
          </svg>
        </a>
        <a href="#" class="nav-link px-2 d-none" id="enable-light-mode" title="Enable light mode">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
            stroke-width="2" stroke="currentColor" fill="none">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
            <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
          </svg>
        </a>

        <div class="kiosk-time" id="current-time"></div>

        <button class="btn-header" id="btn-fullscreen" onclick="toggleFullscreen()">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2">
            <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3" />
          </svg>
          <span id="fullscreen-text">Fullscreen</span>
        </button>

        <button class="btn-header btn-logout" data-bs-toggle="modal" data-bs-target="#logout-modal">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" y1="12" x2="9" y2="12" />
          </svg>
          Logout
        </button>
      </div>
    </div>

    <!-- Main Content Layout -->
    <div class="kiosk-main">
      <!-- Sidebar: Presensi Terakhir -->
      <div class="sidebar-recent" id="sidebar-recent">
        <div class="sidebar-header">
          <h3 class="sidebar-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 5H7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2V7a2 2 0 0 0 -2 -2h-2" />
              <rect x="9" y="3" width="6" height="4" rx="2" />
              <path d="M9 14l2 2l4 -4" />
            </svg>
            Presensi Terakhir
          </h3>
          <p class="sidebar-subtitle">Riwayat presensi sesi ini</p>
        </div>
        <div class="sidebar-content" id="recent-list">
          <div class="sidebar-empty">
            <p>🕐 Belum ada presensi</p>
          </div>
        </div>
      </div>

      <!-- Content Area: State-Based Rendering -->
      <div class="content-area">

        <!-- STATE 1: SCANNER -->
        <div class="state-container active" id="state-scanner">
          <div class="scanner-view">
            <div id="scanner-viewport"></div>
            <div class="scanner-overlay"><span></span></div>
            <div class="scanner-instruction">
              📱 Arahkan Barcode ke Kamera
            </div>
          </div>
        </div>

        <!-- STATE 2: VERIFICATION -->
        <div class="state-container" id="state-verification">
          <div class="verification-view">
            <!-- Pegawai Info -->
            <div class="pegawai-card">
              <img src="" alt="Foto" class="pegawai-foto" id="pegawai-foto">
              <div class="pegawai-details">
                <h2 class="pegawai-nama" id="pegawai-nama"></h2>
                <p class="pegawai-nip" id="pegawai-nip"></p>
              </div>
            </div>

            <!-- Instruction Panel -->
            <div class="instruction-panel">
              <div class="instruction-title">
                💡 Panduan Verifikasi Wajah
              </div>
              <ul class="instruction-list">
                <li>Arahkan wajah ke kamera dengan pencahayaan yang cukup</li>
                <li>Lepas masker, kacamata, atau topi jika perlu</li>
                <li>Posisikan wajah di tengah area kamera</li>
                <li>Tunggu hingga sistem mencocokkan wajah Anda</li>
              </ul>
            </div>

            <!-- Camera -->
            <div class="camera-wrapper">
              <video id="verification-video" autoplay playsinline></video>
              <canvas id="capture-canvas" style="display:none;"></canvas>
            </div>

            <!-- Face Status -->
            <div class="face-status-panel info" id="face-status">
              Memuat kamera...
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
              <button class="btn-action btn-cancel" onclick="resetToScanner()">❌ Batal</button>
            </div>
          </div>
        </div>

        <!-- STATE 3: FAILURE HELP -->
        <div class="state-container" id="state-failure">
          <div class="failure-help">
            <div class="failure-icon">⚠️</div>
            <h2 class="failure-title">Verifikasi Wajah Gagal</h2>
            <p class="failure-message">
              Jika verifikasi gagal karena perubahan wajah atau wajah belum terdaftar, silakan login melalui perangkat
              pribadi Anda dan lakukan <strong>Request Pendaftaran Wajah</strong>.
            </p>

            <div class="qr-section">
              <div class="qr-title">📱 Scan QR Code untuk membuka PresenSi di perangkat pribadi Anda</div>
              <div id="qrcode-container"></div>
              <p class="qr-url" id="qr-url-text"></p>
            </div>

            <div class="action-buttons">
              <button class="btn-action btn-cancel" onclick="resetToScanner()">🔄</button>
            </div>
          </div>
        </div>

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
  // ==================== GLOBAL STATE ====================
  const state = {
    currentMode: 'scanner', // scanner | verification | failure
    currentPegawai: null,
    human: null,
    isModelLoaded: false,
    isFaceVerifying: false,
    isProcessingScan: false,
    lastScanTime: 0,
    detectionInterval: null,
    streamVerification: null,
    baseUrl: '<?= base_url() ?>',
    csrfToken: '<?= csrf_token() ?>',
    csrfHash: '<?= csrf_hash() ?>',
    recentAttendance: [], // Session-based storage (max 10)
    failureTimeout: null
  };

  // ==================== DARK MODE ====================
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

  // ==================== CLOCK ====================
  function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    });
    const clockEl = document.getElementById('current-time');
    if (clockEl) clockEl.textContent = timeString;
  }
  setInterval(updateClock, 1000);
  updateClock();

  // ==================== FULLSCREEN ====================
  function toggleFullscreen() {
    const elem = document.documentElement;
    const btnText = document.getElementById('fullscreen-text');

    if (!document.fullscreenElement) {
      elem.requestFullscreen().then(() => {
        btnText.textContent = 'Exit Fullscreen';
      }).catch(err => console.error('Fullscreen error:', err));
    } else {
      document.exitFullscreen().then(() => {
        btnText.textContent = 'Fullscreen';
      });
    }
  }

  // ==================== STATE MANAGEMENT ====================
  function switchState(newState) {
    // Hide all states
    document.querySelectorAll('.state-container').forEach(el => {
      el.classList.remove('active');
    });

    // Show target state
    const targetEl = document.getElementById('state-' + newState);
    if (targetEl) {
      targetEl.classList.add('active');
    }

    state.currentMode = newState;
  }

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

  // ==================== SCANNER ====================
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
          showConfirmButton: false
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
        showFailureHelp();
        return;
      }

      showVerificationMode();

    } catch (error) {
      console.error(error);
      Swal.fire({
        icon: 'error',
        title: 'Koneksi Error',
        text: 'Terjadi kesalahan jaringan.',
        timer: 3000,
        showConfirmButton: false
      });
      resetToScanner();
    }
  }

  // ==================== VERIFICATION MODE ====================
  async function showVerificationMode() {
    switchState('verification');

    document.getElementById('pegawai-foto').src = state.currentPegawai.foto;
    document.getElementById('pegawai-nama').textContent = state.currentPegawai.nama;
    document.getElementById('pegawai-nip').textContent = 'Nomor Induk: ' + state.currentPegawai.nomor_induk;

    updateFaceStatus('Memuat kamera...', 'info');

    await initVerificationCamera();

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
        showConfirmButton: false
      });
      resetToScanner();
    }
  }

  async function startFaceDetection() {
    const video = document.getElementById('verification-video');
    const startTime = Date.now();
    const MAX_WAIT_TIME = 10000; // 10 detik dalam milidetik

    state.detectionInterval = setInterval(async () => {
      if (state.isFaceVerifying) return;
      if (Date.now() - startTime >= MAX_WAIT_TIME) {
        updateFaceStatus('Waktu verifikasi habis', 'danger');
        clearInterval(state.detectionInterval);
        state.detectionInterval = null;
        showFailureHelp();
        return;
      }

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
      statusEl.className = 'face-status-panel ' + type;
    }
  }

  // ==================== FAILURE HELP STATE ====================
  // ==================== FAILURE HELP STATE ====================
  function showFailureHelp() {
    if (state.streamVerification) {
      state.streamVerification.getTracks().forEach(t => t.stop());
      state.streamVerification = null;
    }

    if (state.detectionInterval) {
      clearInterval(state.detectionInterval);
      state.detectionInterval = null;
    }

    switchState('failure');

    // Generate QR Code for face enrollment
    const enrollmentUrl = state.baseUrl + 'login';
    document.getElementById('qr-url-text').textContent = enrollmentUrl;

    const qrcodeContainer = document.getElementById('qrcode-container');
    qrcodeContainer.innerHTML = ''; // Clear previous QR

    new QRCode(qrcodeContainer, {
      text: enrollmentUrl,
      width: 200,
      height: 200,
      colorDark: "#1e3a8a",
      colorLight: "#ffffff",
      correctLevel: QRCode.CorrectLevel.H
    });

    state.failureTimeout = setTimeout(() => {
      resetToScanner();
    }, 7500);
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
        addToRecentList(result.data, mode, imageData);
        showConfirmation(result.data, mode, result.duplicate);
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: result.message,
          timer: 3000,
          showConfirmButton: false
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
        showConfirmButton: false
      });
      resetToScanner();
    }
  }

  // ==================== RECENT ATTENDANCE (SESSION-BASED) ====================
  function addToRecentList(data, mode, capturedImage) {
    const recentItem = {
      nama: data.nama,
      foto: capturedImage || state.currentPegawai.foto,
      jam: data.jam,
      mode: mode,
      timestamp: Date.now()
    };

    state.recentAttendance.unshift(recentItem);
    if (state.recentAttendance.length > 1000) {
      state.recentAttendance = state.recentAttendance.slice(0, 1000);
    }

    renderRecentList();
  }

  function renderRecentList() {
    const listContainer = document.getElementById('recent-list');

    if (state.recentAttendance.length === 0) {
      listContainer.innerHTML = '<div class="sidebar-empty"><p>🕐 Belum ada presensi</p></div>';
      return;
    }

    let html = '';
    state.recentAttendance.forEach(item => {
      const badgeClass = item.mode === 'masuk' ? 'masuk' : 'keluar';
      const badgeText = item.mode === 'masuk' ? 'Masuk' : 'Keluar';

      html += `
        <div class="recent-item">
          <img src="${item.foto}" alt="${item.nama}" class="recent-avatar">
          <div class="recent-info">
            <div class="recent-name">${item.nama}</div>
            <div class="recent-time">${item.jam}</div>
          </div>
          <span class="recent-badge ${badgeClass}">${badgeText}</span>
        </div>
      `;
    });

    listContainer.innerHTML = html;
  }

  // ==================== CONFIRMATION ====================
  function showConfirmation(data, mode, isDuplicate) {
    if (state.streamVerification) {
      state.streamVerification.getTracks().forEach(t => t.stop());
    }

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
        setTimeout(() => {
          resetToScanner();
        }, 3000);
      }
    });
  }

  // ==================== RESET ====================
  function resetToScanner() {
    if (state.failureTimeout) {
      clearTimeout(state.failureTimeout);
      state.failureTimeout = null;
    }

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

    switchState('scanner');

    setTimeout(() => {
      initScanner();
    }, 100);
  }

  // ==================== INIT ====================
  document.addEventListener('DOMContentLoaded', function() {
    preloadHumanJS();
    initScanner();
    renderRecentList();
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